#!/bin/bash
export ALTAIRIMPORTDIR="/migrationSxaOld";
export ALTAIRDUMPSVNZIP="sxaOld.dump.svn.tgz";
export ALTAIRDUMPSVN="SXA/sxaOld.dump.svn";
export WHAREHOUSE="/var/www/virtual/sxa.svn-warehouse";
export WORKINGCOPY="/var/www/virtual/sxa.svn-wc";
export NEWZUNOGEDSTRUCTDIR="/var/www/virtual/sxa.startx.fr/tmp";
export NEWZUNOGEDSTRUCTFILE="svnData.tar.gz";
export NEWZUNOGEDTMPDIR="/var/www/virtual/sxa.svn-tmp";
#
# la suite des opérations creer l'entrepot depuis le dump
# de l'entrepot altair
#echo "=================================================="
#echo "dézippage de $ALTAIRDUMPSVNZIP"
#cd $ALTAIRIMPORTDIR
#tar xzvf $ALTAIRDUMPSVNZIP
#cd -
#
#echo "=================================================="
#echo "création de l'entrepot $WHAREHOUSE"
#rm -rf $WHAREHOUSE
#svnadmin create $WHAREHOUSE
#svnadmin load $WHAREHOUSE < $ALTAIRIMPORTDIR/$ALTAIRDUMPSVN

echo "=================================================="
echo "création de la copie de travail $WORKINGCOPY"
rm -rf $WORKINGCOPY
svn co file://$WHAREHOUSE $WORKINGCOPY


echo "=================================================="
echo "récupération de la nouvelle structure de GED ZUNO"
rm -rf $NEWZUNOGEDTMPDIR
mkdir $NEWZUNOGEDTMPDIR
cp $NEWZUNOGEDSTRUCTDIR/$NEWZUNOGEDSTRUCTFILE $NEWZUNOGEDTMPDIR
cd $NEWZUNOGEDTMPDIR
tar xzvf $NEWZUNOGEDSTRUCTFILE
rm -f $NEWZUNOGEDSTRUCTFILE
cd $WORKINGCOPY
svn mv ARCHIVES/Affaire ARCHIVES/Affaires
svn mkdir ARCHIVES/Factures
svn mkdir ARCHIVES/Factures/Fournisseurs
svn mv ARCHIVES/Facture ARCHIVES/Factures/Clients
svn mv WORK/Affaire WORK/Affaires
svn mkdir WORK/Factures
svn mkdir WORK/Factures/Fournisseurs
svn mv WORK/Facture WORK/Factures/Clients
mv WORK/Cannevas ../OldCannevasARecuperer
svn rm --force WORK/Cannevas
svn ci -m "Changement de la structure des répertoires pour la migration de SXA vers ZunoSxa"
cp -r $NEWZUNOGEDTMPDIR/WORK/Cannevas WORK/
svn add WORK/Cannevas
svn ci -m "Ajout des nouveaux models de gabarits pour ZunoSxa"
echo "voir le répertoire OldCannevasARecuperer/ pour une récupération manuelle des cannevas"
svn up .
rm -rf $NEWZUNOGEDTMPDIR
chown -R apache:production $WHAREHOUSE $WORKINGCOPY
chmod -R ug+rw $WHAREHOUSE $WORKINGCOPY
