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

#Set gmx to gpu enabled version
gmx=/home/jginnard/bin/gmx

readonly DB_PASSWORD="Gromacs#2017"

select_query=$(cat <<EOF
SELECT
  pdbFileName,
  duration,
  temperature,
  id,
  forceField,
  queuePosition,
  frames
FROM Simulations
WHERE queuePosition = 1 OR queuePosition = 0
ORDER BY queuePosition ASC
EOF
)

update_queue_query=$(cat <<EOF
UPDATE Simulations
SET queuePosition = (queuePosition - 1)
WHERE queuePosition > 0
EOF
)

complete_current_sim_query=$(cat <<EOF
UPDATE Simulations
SET queuePosition = -1
WHERE queuePosition = 0
EOF
)

while true; do
  query_result=$(mysql ProteinSim -u proteinSim -p$DB_PASSWORD -se "$select_query")

  #read query_result into vars
  read pdb_file_name duration temperature id force_field queue_position frames <<< $query_result

  #if result not empty and there isn't a simulation running
  if [ ! -z "$query_result" ] && [ $queue_position -ne 0 ]; then

    #decrement queue positions
    mysql ProteinSim -u proteinSim -p$DB_PASSWORD -se "$update_queue_query"

    #FOR TESTING PURPOSES- REMOVE AFTER TESTING
    echo "pdb_file_name: $pdb_file_name"
    echo "duration: $duration"
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
    steps=$(printf "%.0f" $(echo "500000 * $duration" | bc))
    echo -e "\nnsteps      = $steps\n" >> md.mdp

    #add temperature to nvt.mdp, md.mdp and fec.mdp file copied from default folder
    temp_in_kelvin=$(echo "273.15 + $temperature" | bc)
    if [ "$frames" -eq "0" ]; then
    frame_interval=0
    else
    frame_interval=$(printf "%.0f" $(echo "$steps / $frames" | bc))
    fi

    echo -e "\ngen_temp       = $temp_in_kelvin\n" >> nvt.mdp
    echo -e "\nref_t       = $temp_in_kelvin    $temp_in_kelvin\n" >> nvt.mdp
    echo -e "\nref_t       = $temp_in_kelvin    $temp_in_kelvin\n" >> md.mdp
    echo -e "\nref_t       = $temp_in_kelvin    $temp_in_kelvin\n" >> fec.mdp
    echo -e "\nnstxout       = $frame_interval\n" >> md.mdp
    echo -e "\nnstvout       = $frame_interval\n" >> md.mdp

    #copy protein file (protein.pdb)
    path_to_protein_file="/var/www/html/ProteinSimulations/uploads/$pdb_file_name"
    cp -f $path_to_protein_file "$current_sim_path/protein.pdb"

    #capture simulation start time
    sim_start=$(date +"%Y/%m/%e %H:%M:%S")

    #add sim start time to db
    mysql ProteinSim -u proteinSim -p$DB_PASSWORD -se "UPDATE Simulations SET startTime=STR_TO_DATE(\"$sim_start\", '%Y/%m/%d %k:%i:%s') WHERE id=$id"


    #give the simulation data to gromacs
    echo -e "$force_field\n1\n1\n1\n1\n1\n1\n1\n1\n1\n1\n" | $gmx pdb2gmx -f protein.pdb -o protein.gro -water spc -ter -missing
    #select '1' for force field selection
    #select '1' for all -ter options, there is 1 for each terminus. 10 just to be safe.

    #Run Gromacs Simulations
    $gmx editconf -f protein.gro -o newbox.gro -bt dodecahedron -d 1.5
    $gmx solvate -cp newbox.gro -cs spc216.gro -p topol.top -o solv.gro
    $gmx grompp -f em.mdp -c solv.gro -p topol.top -o ions.tpr
    echo -e "13" | $gmx genion -s ions.tpr -o solv_ions.gro -p topol.top -pname ZN -np 3
    #enter '13' SOL  Replaces Solvent molecules with 3 ZN molecules
    $gmx grompp -f em_real.mdp -c solv_ions.gro -p topol.top -o em.tpr

    $gmx mdrun -v -deffnm  em -ntmpi 8 -gpu_id 00000000 -nb gpu_cpu
    echo -e "1\n11\nq" | gmx make_ndx -f em.gro -o index.ndx
    #enter '1' Protein
    #enter '11' non-Protein
    #enter 'q' quit
    $gmx grompp -f nvt.mdp -c em.gro -p topol.top -n index.ndx -o nvt.tpr
    $gmx mdrun -deffnm nvt -ntmpi 8 -gpu_id 00000000 -nb gpu_cpu
    $gmx grompp -f npt.mdp -c nvt.gro -t nvt.cpt -p topol.top -n index.ndx -o npt.tpr
    $gmx mdrun -deffnm npt -ntmpi 8 -gpu_id 00000000 -nb gpu_cpu

    #Production Simulation, variable time
    $gmx grompp -f md.mdp -c npt.gro -t npt.cpt -p topol.top -n index.ndx -o md_0_1.tpr
    $gmx mdrun -deffnm md_0_1 -ntmpi 8 -gpu_id 00000000 -nb gpu_cpu
    #This causes crash. Run at end of longer simulation to calculate Free Energy
    #$gmx grompp -f fec.mdp -c md_0_1.gro -t md_0_1.cpt -p topol.top -n index.ndx -o fec.tpr
    #$gmx mdrun -deffnm fec -ntmpi 8 -gpu_id 00000000 -nb gpu_cpu

    #Generate all the output files
    echo -e "11 0\n" | $gmx energy -f em.edr -o em_potential.xvg
    echo -e "15 0\n" | $gmx energy -f nvt.edr -o nvt_temperature.xvg
    echo -e "17 0\n" | $gmx energy -f npt.edr -o npt_pressure.xvg
    echo -e "23 0\n" | $gmx energy -f npt.edr -o npt_density.xvg

    echo -e "10 0\n" | $gmx energy -f md_0_1.edr -o md_potential.xvg
    echo -e "13 0\n" | $gmx energy -f md_0_1.edr -o md_temperature.xvg
    echo -e "15 0\n" | $gmx energy -f md_0_1.edr -o md_pressure.xvg
    echo -e "21 0\n" | $gmx energy -f md_0_1.edr -o md_density.xvg
    echo -e "1 2 3 4 5 6 7 8 9 10 11 12 13 14 15 16 17 18 19 20 21 22 23 24 25 26 27 28 29 30 31 32 33 34 35 36 37 38 39 40 41 42 43 44 45 46 47 48 49 50 51 52 53 54 55 56 57 58 59 60 61 0\n" | $gmx energy -f md_0_1.edr -o md_all_data.xvg

    echo -e "1 0\n" | $gmx gyrate -s md_0_1.tpr -f md_0_1_noPBC.xtc -o gyrate.xvg
    echo -e "0 0\n" | $gmx trjconv -s md_0_1.tpr -f md_0_1.xtc -o md_0_1_noPBC.xtc -pbc mol -ur compact
    echo -e "4\n4\n" | $gmx rms -s md_0_1.tpr -f md_0_1_noPBC.xtc -o rmsd_backbone.xvg -tu ns
    echo -e "4\n4\n" | $gmx rms -s em.tpr -f md_0_1_noPBC.xtc -o rmsd_backbone_crystal.xvg -tu ns
    #$gmx bar -g fec.edr -o -oi -oh

    #convert md_0_1.gro to pdb
    $gmx editconf -f md_0_1.gro -o protein_after.pdb
    #Remove all solvent from pdb file
    grep -v "SOL\|ZN" protein_after.pdb > temp && mv temp protein_after.pdb
    #capture simulation end time
    sim_end=$(date +"%Y/%m/%e %H:%M:%S")

    #add sim end time to db
    mysql ProteinSim -u proteinSim -p$DB_PASSWORD -se "UPDATE Simulations SET endTime=STR_TO_DATE(\"$sim_end\", '%Y/%m/%d %k:%i:%s') WHERE id=$id"

    #mark current simulation as complete
    mysql ProteinSim -u proteinSim -p$DB_PASSWORD -se "$complete_current_sim_query"

    #copy bar.xvg to new dir
    result_folder_path="/var/www/html/ProteinSimulations/results/sim$id"
    mkdir $result_folder_path

    #move non-zipped .xvg files to result folder
    cp ./{bar.xvg,md_potential.xvg,md_temperature.xvg,md_pressure.xvg,md_density.xvg,protein.pdb,protein_after.pdb} $result_folder_path

    #put all .xvg, .gro, .pdb, .trr, .log  files into a zipped file in new dir
    zip -rj "$result_folder_path/simulation_data.zip" . -i '*.xvg' '*.gro' '*.trr' '*.pdb' '*.log'

    #Notify user the simulation is complete
    email_query="SELECT Users.firstName, Users.lastName, Users.email, Simulations.simulationName FROM Users INNER JOIN Simulations ON Users.username=Simulations.username WHERE Simulations.id=$id"
    query_result=$(mysql ProteinSim -u proteinSim -p$DB_PASSWORD -se "$email_query")
    #read query_result into vars
    read firstName lastName email simulationName<<< $query_result

    subject="Simulation Complete: $simulationName"
    link="https://jeremyginnard.com/ProteinSimulations/resultPage.php?id=$id"
    message="Greetings $firstName $lastName,"$'\n\n'"Your simulation titled \"$simulationName\" is complete. You can view the results at the following link:"$'\n'"$link"
    #Send the email
    java -jar /home/gromacs/simulations/sendEmail.jar "$email" "$subject" "$message"
    echo "Email parameters = "
    echo $email $subject $message

    #remove simulation configuration files
    rm $path_to_protein_file
    rm -rf $current_sim_path

  else
    break   #no more incomplete simulations OR a sim is already running
  fi
done
