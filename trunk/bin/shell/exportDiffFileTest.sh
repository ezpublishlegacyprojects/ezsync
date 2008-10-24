#!/bin/bash
shopt -s expand_aliases;
#creo i 2 file diff
echo "pippo pippo pippo" > testFile1.txt
echo "pippo2 pippo2 pippo2" > testFile2.txt

#creo l'elenco dei file
echo "M    testFile1.txt
A    testFile2.txt
D    testFile3.txt
 M   ." >listFileDiff.txt

#creo l'elenco dei file del tar
echo "testFile1.txt
testFile2.txt" >listTarFile.txt

echo "Archivio delle differenze 'diff.tar.gz' creato
File da cancellare
D    testFile3.txt
Fine" >outputCheck.txt

#lancio lo script
./exportDiffFile.sh 1 2 test>output.txt

#verifico la presenza del tgz e la dimensione
if [ -e diff.tar.gz ]; then
  echo "[OK] Il file tgz esiste"
else
  echo "[ERRORE] Il file tgz NON esiste"
fi

# verifico l'output dei file cancellati
if [ '0' -eq `diff output.txt outputCheck.txt|wc -w` ]; then
  echo "[OK] File cancellati"
else
  echo "[ERROR] File cancellati"
fi

tar --list -f diff.tar.gz >listTarFileCheck.txt

if [ '0' -eq `diff output.txt outputCheck.txt|wc -w` ]; then
  echo "[OK] File tgzippati"
else
  echo "[ERROR] File tgzippati"
fi

rm diff.tar.gz listFileDiff.txt listTarFile.txt output.txt testFile2.txt listTarFileCheck.txt outputCheck.txt testFile1.txt


