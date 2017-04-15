#!/bin/bash
#
# This file will be executed from a cron job
# and will run all incomplete simulations in db
# SHELL DEPENDENCIES:
#   zip

readonly DB_PASSWORD="Gromacs#2017"

select_query=$(cat <<EOF
SELECT 
  mutations, 
  pdbFileName, 
  pdbFile, 
  simulationName, 
  temperature 
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
    read mutations pdb_file_name pdb_file simulation_name temperature <<< $query_result

    #if dir doesn't exist (sim hasn't run before)
    if [ ! -d "$PWD/test_directory/$simulation_name" ]; then 
      #create dir
      mkdir -p $PWD/test_directory/$simulation_name

      #give the simulation data to gromacs

      #decrement queue position of incomplete simulations
      mysql ProteinSim -u root -p$DB_PASSWORD -se "$update_query"

          #move gromacs result files to the new dir
          mv $PWD/test_directory/gromacs_result_files/* $PWD/test_directory/$simulation_name/

      #copy bar.xvg to new dir
      find ./test_directory/gromacs_result_files -name "bar.xvg" -exec cp {} ./test_directory/sim_dir \;

      #put all .xvg, .gro, .pdb, .trr files into a zipped file in new dir

    else
      break   #this sim already ran
    fi
  else
    break   #no more incomplete simulations
  fi
done


#T E M P O R A R Y  N O T E S
#I need to copy all xvg files to the simulation's directory by in plain text 
#but also all xvg, gro, pdb, and trr files into a zipped file in that same 
#directory
#
#