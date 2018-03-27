#!/bin/bash

export PATH=/home/pdadmin/scripts:$PATH

# Try PPG first
# MRDIRS=$(find /mnt/pdresearch/Studies/PPG/ -maxdepth 2 -mindepth 2 -type d -name "MR")

# Now try amcog
MRDIRS=$(find /mnt/pdresearch/Studies/AMCOG/ -maxdepth 2 -mindepth 2 -type d -name "MR")

for DIR in $MRDIRS
do
	SUBJ=$(basename `dirname $DIR`)
	#SUBJ=${SUBJ/NC/PPG-NC}
	#SUBJ=${SUBJ/PAT/PPG-PAT}
	SUBJ=${SUBJ/AMCOG/AMCOG-0}
	if [ -z "${SUBJ}" ]
	then
		continue
	fi
	PSCID=$(getpscidfromsubjid.sh $SUBJ)
	#AVISIT=$(aggregate_visitnum.sh $SUBJ PPG 1)
	AVISIT=$(aggregate_visitnum.sh $SUBJ AMCOG 1)

	#NEWDIR="/var/www/loris/htdocs/MRI/${PSCID}/a/"
	NEWDIR="/var/www/loris/htdocs/MRI/${PSCID}/${AVISIT}/"
	#if [ ! -d "${NEWDIR}" ]
	#then
		mkdir -p $NEWDIR
		ln -s $DIR $NEWDIR

		CANDID=$(getcandidfromsubjid.sh $SUBJ)
		#CODELIST=$(do_sql.sh "select value from parameter_candidate where candid=${CANDID};")
		CMDCODE="select codelist from visit where commentid like '${CANDID}%' order by Date_taken"
		CODES=$(do_sql.sh "${CMDCODE}" | head -n$AVISIT | tail -n1)

		#CMD="insert into tarchive (\`patientid\`,\`patientname\`,\`archivelocation\`) values ('${PSCID}','${CODES}','MRI/${PSCID}/${AVISIT}/MR');"
		CMD="insert into tarchive (\`patientid\`,\`patientname\`,\`archivelocation\`,\`patientgender\`) values ('${PSCID}','${CODES}','MRI/${PSCID}/${AVISIT}/MR','${AVISIT}');"
		do_sql.sh "${CMD}"
	#fi

done

