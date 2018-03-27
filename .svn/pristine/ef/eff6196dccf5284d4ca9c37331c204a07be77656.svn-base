#!/bin/bash

SUBJID=$1

mysql -ulorisuser -plorisuser -Dlorisdb<< EOF
 
	#select * from smell where PSCID = '${SUBJID}'; 
	select c.PSCID,c.candID,s.* from candidate c  
	join smell s on c.CandID = (select left(s.commentID,6))
	where c.PSCID=$SUBJID and Data_entry_completion_status="Complete";

EOF