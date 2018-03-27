#!/bin/bash

# Something like
#
# move_visit.sh 0461 1 0353 3

ORIG_PSCID=$1
ORIG_VISITNUM=$2

NEW_PSCID=$3
NEW_VISITNUM=$4

# Default to sandbox DB (may change later)
DB=loris_sand2
if [ ! -z $5 ]
then
	DB=$5
fi


# Get both candidate IDs
ORIG_CANDID=$(getcandidfrompscid.sh $ORIG_PSCID)
NEW_CANDID=$(getcandidfrompscid.sh $NEW_PSCID)

# Check to see if the visit to move to already exists, for now just abort.
QUERY="select count(*) from session where candid=${NEW_CANDID} and VisitNo=${NEW_VISITNUM}"
COUNT=$(do_sql.sh "$QUERY" $DB)

if [ $COUNT -gt 0 ]
then
	echo -e "\nVisit already exists, aborting!"
	exit
fi

# Alter session table
QUERY="select ID from session where candid=${ORIG_CANDID} and VisitNo=${ORIG_VISITNUM}"
SESSIONID=$(do_sql.sh "$QUERY" $DB)

CMD="update session set CandID=${NEW_CANDID}, VisitNo=${NEW_VISITNUM}, Visit_label='V${NEW_VISITNUM}' where ID=${SESSIONID}"
echo $CMD

do_sql.sh "${CMD}" $DB

# Update all the commentIDs in the test tables
STROLD=$ORIG_CANDID$ORIG_PSCID$SESSIONID
STRNEW=$NEW_CANDID$NEW_PSCID$SESSIONID

# Instruments
TESTS=$(do_sql.sh "select Test_name from test_names;" $DB)

for INST in $TESTS
do
	CMD1="select commentid from ${INST} where commentid like '$STROLD%'"
	CID=$(do_sql.sh "select commentid from ${INST} where commentid like '$STROLD%'" $DB)
	DDECID=$(do_sql.sh "select commentid from ${INST} where commentid like 'DDE_$STROLD%'" $DB)

	NEWCID=${CID/$STROLD/$STRNEW}


	CMD1="update ${INST} set commentid='${NEWCID}' where commentid = '${CID}'"
	CMD2="update ${INST} set commentid='DDE_${NEWCID}' where commentid = '${DDECID}'"

	
	do_sql.sh "${CMD1}" $DB
	do_sql.sh "${CMD2}" $DB

	# Flag
	do_sql.sh "update flag set commentid='${NEWCID}' where commentid = '${CID}'" $DB
	do_sql.sh "update flag set commentid='DDE_${NEWCID}' where commentid = '${DDECID}'" $DB
done

# If the orig subject now has no visits left, delete it.
CMD="select count(*) from session where candid='${ORIG_CANDID}'"
NUMVISITS_ORIG=$(do_sql.sh "${CMD}" $DB)

if [ $NUMVISITS_ORIG -eq 0 ]
then
	echo -e "\nDeleting original subject"
	delete_subject.sh $ORIG_CANDID $DB
fi
