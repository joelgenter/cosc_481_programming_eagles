#!/bin/bash
#
# This file will be executed from a php file. The PHP file will only
# execute this file when it knows there is a new simulation and there are
# no simulations currently running. This script will execute all incomplete
# simulations in the db
#   
# SHELL DEPENDENCIES:
#   zip
#   gromacs

readonly DB_PASSWORD="Gromacs#2017"

select_query=$(cat <<EOF
SELECT
  mutations,
  pdbFileName,
  duration,
  simulationName,
  temperature,
  id
FROM Simulations
WHERE queuePosition = 1
EOF
)

update_query=$(cat <<EOF
UPDATE Simulations
SET queuePosition = (queuePosition - 1)
WHERE queuePosition >= 0
EOF
)

while true; do
  query_result=$(mysql ProteinSim -u proteinSim -p$DB_PASSWORD -se "$select_query")

  if [ ! -z "$query_result" ]; then    #if result not empty
    #read query_result into vars
    read mutations pdb_file_name duration simulation_name temperature id <<< $query_result

    #copy default gromacs files to current simulation folder
    current_sim_path='/home/gromacs/simulations/current_simulation'
    mkdir $current_sim_path
    cp -rf /home/gromacs/simulations/default/* $current_sim_path
    cd /home/gromacs/simulations/current_simulation

    #place pdb_file from blob into file (protein.pdb)
    path_to_protein_file="/var/www/html/ProteinSimulations/uploads/$pdb_file_name"
    cp -f $path_to_protein_file "$current_sim_path/protein.pdb"

    #give the simulation data to gromacs
    echo -e "9\n1\n1\n1\n1\n1\n1\n1\n1\n1\n1\n" | gmx pdb2gmx -f protein.pdb -o protein.gro -water spc -ter -missing
    #select '1' for force field selection
    #select '1' for all -ter options, there is 1 for each terminus. 10 just to be safe.
    
    gmx editconf -f protein.gro -o newbox.gro -bt dodecahedron -d 1.5
    gmx solvate -cp newbox.gro -cs spc216.gro -p topol.top -o solv.gro
    gmx grompp -f em.mdp -c solv.gro -p topol.top -o ions.tpr
    echo -e "13" | gmx genion -s ions.tpr -o solv_ions.gro -p topol.top -pname ZN -np 3
    #enter '13' SOL  Replaces Solvent molecules with 3 ZN molecules
    gmx grompp -f em_real.mdp -c solv_ions.gro -p topol.top -o em.tpr
    gmx mdrun -v -deffnm em -ntmpi 8 -gpu_id 00000000 -nb gpu_cpu
    echo -e "1\n11\nq" | gmx make_ndx -f em.gro -o index.ndx
    #enter '1' Protein
    #enter '11' non-Protein
    #enter 'q' quit
    gmx grompp -f nvt.mdp -c em.gro -p topol.top -n index.ndx -o nvt.tpr
    gmx mdrun -deffnm nvt -ntmpi 8 -gpu_id 00000000 -nb gpu_cpu
    gmx grompp -f npt.mdp -c nvt.gro -t nvt.cpt -p topol.top -n index.ndx -o npt.tpr
    gmx mdrun -deffnm npt -ntmpi 8 -gpu_id 00000000 -nb gpu_cpu

    #Production Simulation, variable time
    gmx grompp -f md.mdp -c npt.gro -t npt.cpt -p topol.top -n index.ndx -o md_0_1.tpr
    gmx mdrun -deffnm md_0_1 -ntmpi 8 -gpu_id 00000000 -nb gpu_cpu
    #This causes crash. Run at end of longer simulation to calculate Free Energy
    gmx grompp -f fec.mdp -c md_0_1.gro -t md_0_1.cpt -p topol.top -n index.ndx -o fec.tpr
    gmx mdrun -deffnm fec -ntmpi 8 -gpu_id 00000000 -nb gpu_cpu

    echo -e "11 0\n" | gmx energy -f em.edr -o em_potential.xvg
    echo -e "15 0\n" | gmx energy -f nvt.edr -o nvt_temperature.xvg
    echo -e "17 0\n" | gmx energy -f npt.edr -o npt_pressure.xvg
    echo -e "23 0\n" | gmx energy -f npt.edr -o npt_density.xvg

    echo -e "11 0\n" | gmx energy -f md_0_1.edr -o md_potential.xvg
    echo -e "14 0\n" | gmx energy -f md_0_1.edr -o md_temperature.xvg
    echo -e "16 0\n" | gmx energy -f md_0_1.edr -o md_pressure.xvg
    echo -e "22 0\n" | gmx energy -f md_0_1.edr -o md_density.xvg

    echo -e "1 0\n" | gmx gyrate -s md_0_1.tpr -f md_0_1_noPBC.xtc -o gyrate.xvg
    echo -e "0 0\n" | gmx trjconv -s md_0_1.tpr -f md_0_1.xtc -o md_0_1_noPBC.xtc -pbc mol -ur compact
    echo -e "4\n4\n" | gmx rms -s md_0_1.tpr -f md_0_1_noPBC.xtc -o rmsd_backbone.xvg -tu ns
    echo -e "4\n4\n" | gmx rms -s em.tpr -f md_0_1_noPBC.xtc -o rmsd_backbone_crystal.xvg -tu ns

    #decrement queue position of incomplete simulations
    mysql ProteinSim -u proteinSim -p$DB_PASSWORD -se "$update_query"

    #copy bar.xvg to new dir
    result_folder_path="/var/www/html/ProteinSimulations/results/sim$id"
    mkdir $result_folder_path
    find . -name "bar.xvg" -exec cp {} $result_folder_path \;

    #put all .xvg, .gro, .pdb, .trr files into a zipped file in new dir
    zip -rj "$result_folder_path/simulation_data.zip" . -i '*.xvg' '*.gro' '*.trr' '*.pdb'

    #remove simulation configuration files
#    rm $path_to_protein_file
 #   cd ..;
  #  rm -rf -- current_simulation
  else
    break   #no more incomplete simulations
  fi
done
