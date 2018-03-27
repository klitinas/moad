#!/bin/bash

# Designed to find comment id of same visit between two tests

CIDIN=$1
TESTIN=$2
TEST=$3

TESTIDIN=$(do_sql.sh "select ID from test_names where Test_name='${TESTIN}'");
TESTIDOUT=$(do_sql.sh "select ID from test_names where Test_name='${TEST}'");
NUMCHARS=$(echo $TESTIDIN | wc -c)
NUMSTRIP=$(expr $NUMCHARS + 10)

#PAT=$(echo $CIDIN | rev | cut -c 11- | rev)
PAT=$(echo $CIDIN | rev | cut -c $NUMSTRIP- | rev)
CMD="select CommentID from ${TEST} where CommentID like '${PAT}${TESTIDOUT}%'";
CIDOUT=$(do_sql.sh "${CMD}")
echo $CIDOUT
