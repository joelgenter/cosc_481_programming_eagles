#!/bin/bash

#The following code should only be used if we aren't able to use db triggers
# SIMULATION_IS_QUEUED=0;
# while [$SIMULATION_IS_QUEUED -eq 0]
# do
#     myvar=$(mysql testdb -u root -ptestpass -se "SELECT EXISTS(SELECT 1 FROM users WHERE firstName = 'Joel')")

    myvar=$(mysql ProteinSim -u root -prepublic -se "SELECT * FROM Simulations")

    echo $myvar;

#while querying the db for a simulation in queue position 0 returns a result

    #give the simulation info to gromacs to run the simulation

    #query the db changing the completed simulation's queue position to -1 (completed)

#end while
