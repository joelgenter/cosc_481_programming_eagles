#!/bin/bash
#
# This file will be executed from a cron job
# and will run all incomplete simulations in db

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

      #move gromacs result files to the new dir
      mv $PWD/test_directory/gromacs_result_files/* $PWD/test_directory/$simulation_name/
    else
      break   #this sim already ran
    fi
  else
    break   #no more incomplete simulations
  fi
done
