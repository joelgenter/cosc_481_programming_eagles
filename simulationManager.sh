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
  pdbFile,
  simulationName,
  temperature,
  id
FROM Simulations
WHERE queuePosition = 0
EOF
)

update_query=$(cat <<EOF
UPDATE Simulations
SET queuePosition = (queuePosition - 1)
WHERE queuePosition >= 0
EOF
)

while true; do
  query_result=$(mysql ProteinSim -u root -p$DB_PASSWORD -se "$select_query")

  if [ ! -z "$query_result" ]; then    #if result not empty
    #read query_result into vars
    read mutations pdb_file_name pdb_file simulation_name temperature, id <<< $query_result

    #copy default gromacs files to current simulation folder
    cp -rf /home/gromacs/simulations/default/* /home/gromacs/simulations/current_simulation/
    cd /home/gromacs/simulations/current_simulation

    #give the simulation data to gromacs
    gmx pdb2gmx -f $pdbFile -o protein.gro -water spc -ter -missing
    #select '1' for force field selection
    echo | 1
    #select '1' for all -ter options, there is 1 for each terminus. 10 just to be safe.
    echo | 1
    echo | 1
    echo | 1
    echo | 1
    echo | 1
    echo | 1
    echo | 1
    echo | 1
    echo | 1
    echo | 1
    gmx editconf -f protein.gro -o newbox.gro -bt dodecahedron -d 1.5
    gmx solvate -cp newbox.gro -cs spc216.gro -p topol.top -o solv.gro
    gmx grompp -f em.mdp -c solv.gro -p topol.top -o ions.tpr
    gmx genion -s ions.tpr -o solv_ions.gro -p topol.top -pname ZN -np 3
    #enter '13' SOL  Replaces Solvent molecules with 3 ZN molecules
    gmx grompp -f em_real.mdp -c solv_ions.gro -p topol.top -o em.tpr
    gmx mdrun -v -deffnm em -ntmpi 8 -gpu_id 00000000 -nb gpu_cpu
    gmx make_ndx -f em.gro -o index.ndx
    #enter '1' Protein
    echo | 1
    #enter '11' non-Protein
    echo | 11
    #enter 'q' quit
    echo | q
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

    gmx energy -f em.edr -o em_potential.xvg
    gmx energy -f em.edr -o em_temperature.xvg
    gmx energy -f em.edr -o em_pressure.xvg
    gmx energy -f em.edr -o em_density.xvg

    gmx energy -f md_0_1.edr -o md_potential.xvg
    gmx energy -f md_0_1.edr -o md_temperature.xvg
    gmx energy -f md_0_1.edr -o md_pressure.xvg
    gmx energy -f md_0_1.edr -o md_density.xvg

    gmx gyrate -s md_0_1.tpr -f md_0_1_noPBC.xtc -o gyrate.xvg
    gmx trjconv -s md_0_1.tpr -f md_0_1.xtc -o md_0_1_noPBC.xtc -pbc mol -ur compact
    gmx rms -s md_0_1.tpr -f md_0_1_noPBC.xtc -o rmsd.xvg -tu ns
    gmx rms -s em.tpr -f md_0_1_noPBC.xtc -o rmsd_xtal.xvg -tu ns

    #decrement queue position of incomplete simulations
    mysql ProteinSim -u root -p$DB_PASSWORD -se "$update_query"

    #copy bar.xvg to new dir
    result_folder_path="/var/www/html/ProteinSimulations/results/sim$id"
    mkdir $result_folder_path
    find . -name "bar.xvg" -exec cp {} $result_folder_path \;

    #put all .xvg, .gro, .pdb, .trr files into a zipped file in new dir
    zip -rj "$result_folder_path/simulation_data.zip" . -i '*.xvg' '*.gro' '*.trr' '*.pdb'

    #remove simulation configuration files
    rm -rf *
  else
    break   #no more incomplete simulations
  fi
done