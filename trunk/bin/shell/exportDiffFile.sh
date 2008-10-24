#/bin/bash
#
# Creates tgz file with the differences between svn revision passed by argument
#
# NOTE: remember to commit before lauching the script to catch the latest changes!
#
if [ $# -eq 3 ]; then
  function svn {
	cat listFileDiff.txt
  }
fi

if [ $# -lt 2 ]; then
  echo "Usage:"
  echo "exportDiffFile.sh start_rev end_rev"
  exit
fi;

svn diff -r $1:$2 --summarize >diff.file
cat diff.file|head -n -1|grep -v "^D"|awk '{ print $2 }' >diff.ok.file

for f in $( cat  diff.ok.file ); do
    if [ ! -d $f ]; then
	echo $f >>diff.ok2.file
    fi
done

tar czf diff.tar.gz -T diff.ok2.file
rm diff.ok.file
rm diff.ok2.file

echo "Archivio delle differenze 'diff.tar.gz' creato"
# cerca i file da cancellare
echo "File da cancellare"
cat  diff.file|grep "^D"
rm diff.file
echo "Fine"


