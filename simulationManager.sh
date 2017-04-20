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
#   bc

readonly DB_PASSWORD="Gromacs#2017"

select_query=$(cat <<EOF
SELECT
  pdbFileName,
  duration,
  temperature,
  id,
  forceField
FROM Simulations
WHERE queuePosition = 1
EOF
)

update_queue_query=$(cat <<EOF
UPDATE Simulations
SET queuePosition = (queuePosition - 1)
WHERE queuePosition >= 0
EOF
)

while true; do
  query_result=$(mysql ProteinSim -u proteinSim -p$DB_PASSWORD -se "$select_query")

  if [ ! -z "$query_result" ]; then    #if result not empty
    #read query_result into vars
    read pdb_file_name duration temperature id force_field<<< $query_result

    #FOR TESTING PURPOSES- REMOVE AFTER TESTING
    echo "mutations: $mutations"
    echo "pdb_file_name: $pdb_file_name"
    echo "duration: $duration"
    echo "simulation_name: $simulation_name"
    echo "temperature: $temperature"
    echo "id: $id"
    echo "force_field: $force_field"
    #FOR TESTING PURPOSES- REMOVE AFTER TESTING

    #copy default gromacs files to current simulation folder
    current_sim_path='/home/gromacs/simulations/current_simulation'
    mkdir $current_sim_path
    cp -rf /home/gromacs/simulations/default/* $current_sim_path
    cd /home/gromacs/simulations/current_simulation

    #add duration to md.mdp file copied from default folder
    echo -e "\nnsteps      = $(echo "500000 * $duration" | bc)\n" >> md.mdp

    #add temperature to nvt.mdp, md.mdp and fec.mdp file copied from default folder
    temp_in_kelvin=$(echo "273.15 + $temperature" | bc)
    echo -e "\ngen_temp       = $temp_in_kelvin\n" >> nvt.mdp
    echo -e "\nref_t       = $temp_in_kelvin    $temp_in_kelvin\n" >> nvt.mdp
    echo -e "\nref_t       = $temp_in_kelvin    $temp_in_kelvin\n" >> md.mdp
    echo -e "\nref_t       = $temp_in_kelvin    $temp_in_kelvin\n" >> fec.mdp


    #copy protein file (protein.pdb)
    path_to_protein_file="/var/www/html/ProteinSimulations/uploads/$pdb_file_name"
    cp -f $path_to_protein_file "$current_sim_path/protein.pdb"

    #capture simulation start time
    sim_start=date +"%Y/%m/%e %H:%M:%S"

    #give the simulation data to gromacs
    echo -e "$force_field\n1\n1\n1\n1\n1\n1\n1\n1\n1\n1\n" | gmx pdb2gmx -f protein.pdb -o protein.gro -water spc -ter -missing
    #select '1' for force field selection
    #select '1' for all -ter options, there is 1 for each terminus. 10 just to be safe.

    #Generate all the output FILTER_SANITIZE_URL
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

    #Generate all the output files
    echo -e "11 0\n" | gmx energy -f em.edr -o em_potential.xvg
    echo -e "15 0\n" | gmx energy -f nvt.edr -o nvt_temperature.xvg
    echo -e "17 0\n" | gmx energy -f npt.edr -o npt_pressure.xvg
    echo -e "23 0\n" | gmx energy -f npt.edr -o npt_density.xvg

    echo -e "11 0\n" | gmx energy -f md_0_1.edr -o md_potential.xvg
    echo -e "14 0\n" | gmx energy -f md_0_1.edr -o md_temperature.xvg
    echo -e "16 0\n" | gmx energy -f md_0_1.edr -o md_pressure.xvg
    echo -e "22 0\n" | gmx energy -f md_0_1.edr -o md_density.xvg
    echo -e "1 2 3 4 5 6 7 8 9 10 11 12 13 14 15 16 17 18 19 20 21 22 23 24 25 26 27 28 29 30 31 32 33 34 35 36 37 38 39 40 41 42 43 44 45 46 47 48 49 50 51 52 53 54 55 56 57 58 59 60 61 0\n" | gmx energy -f md_0_1.edr -o md_all_data.xvg

    echo -e "1 0\n" | gmx gyrate -s md_0_1.tpr -f md_0_1_noPBC.xtc -o gyrate.xvg
    echo -e "0 0\n" | gmx trjconv -s md_0_1.tpr -f md_0_1.xtc -o md_0_1_noPBC.xtc -pbc mol -ur compact
    echo -e "4\n4\n" | gmx rms -s md_0_1.tpr -f md_0_1_noPBC.xtc -o rmsd_backbone.xvg -tu ns
    echo -e "4\n4\n" | gmx rms -s em.tpr -f md_0_1_noPBC.xtc -o rmsd_backbone_crystal.xvg -tu ns
    gmx bar -g md_0_1.edr -o -oi -oh

    #capture simulation end time
    sim_end=date +"%Y/%m/%e %H:%M:%S"

    #add sim start and end time to db
    mysql ProteinSim -u proteinSim -p$DB_PASSWORD -se "$update_sim_time_query"

    #decrement queue position of incomplete simulations
    mysql ProteinSim -u proteinSim -p$DB_PASSWORD -se "UPDATE Simulations SET startTime=STR_TO_DATE('$sim_start', '%Y/%m/%d %k:%i:%s'), endTime=STR_TO_DATE('$sim_end', '%Y/%m/%d %k:%i:%s') WHERE id=$id"

    #copy bar.xvg to new dir
    result_folder_path="/var/www/html/ProteinSimulations/results/sim$id"
    mkdir $result_folder_path

    #move non-zipped .xvg files to result folder
    cp ./{bar.xvg,md_potential.xvg,md_temperature.xvg,md_pressure.xvg,md_density.xvg} $result_folder_path

    #put all .xvg, .gro, .pdb, .trr files into a zipped file in new dir
    zip -rj "$result_folder_path/simulation_data.zip" . -i '*.xvg' '*.gro' '*.trr' '*.pdb' '*.log'

    #remove simulation configuration files
#    rm $path_to_protein_file
     cd ..;
     rm -rf -- current_simulation
  else
    break   #no more incomplete simulations
  fi
done
