#/bin/bash
#
# Creates tgz file with the differences between svn revision passed by argument
#
# NOTE: remember to commit before lauching the script to catch the latest changes!
#

NAME="diff"

if [ $3 ]; then
  NAME=$3
fi

if [ $# -eq 4 ]; then
  function svn {
	cat listFileDiff.txt
  }
fi

if [ $# -lt 2 ]; then
  echo "Usage:"
  echo "exportDiffFile.sh start_rev end_rev"
  exit
fi;

svn diff -r $1:$2 --summarize >$NAME.file
cat $NAME.file|head -n -1|grep -v "^D"|awk '{ print $2 }' >$NAME.ok.file

FILESIZE=$(stat -c%s "$NAME.ok.file")

if [ $FILESIZE -eq 0 ]; then
  echo "Nessuna modifica nel repository"
  rm $NAME.file
  rm $NAME.ok.file
  exit
fi

for f in $( cat  $NAME.ok.file ); do
    if [ ! -d $f ]; then
	echo $f >>$NAME.ok2.file
    fi
done

tar cf $NAME.tar -T $NAME.ok2.file
rm $NAME.ok.file
rm $NAME.ok2.file

echo "Archivio delle differenze '$NAME.tar' creato"
# cerca i file da cancellare
echo "File da cancellare"
cat  $NAME.file|grep "^D" > delete_diff.txt
tar rf $NAME.tar delete_diff.txt
gzip $NAME.tar
rm $NAME.file
rm delete_diff.txt
echo "Fine"


