#!/bin/bash
SIMULATION_IS_QUEUED=0;
while [$SIMULATION_IS_QUEUED -eq 0]
do
    myvar=$(mysql testdb -u root -ptestpass -se "SELECT EXISTS(SELECT 1 FROM users WHERE firstName = 'Joel')")
echo $myvar;