#!/usr/bin/perl

use warnings;

system( "mkdir tmp_md classes" );
system( "rm -rf ../../api.wiki/classes" );
print "Updating doxygen documentation.\n";
system( "doxygen DoxyfileConf &> /dev/null" );
print "Generating GitHub Wiki documentation.\n";

@list = <html/*class*.html>;

foreach (@list) {
	my $inputFile = $_;
	my $outputFile = "tmp_md/" . substr($_, 5, length() - 9) . "txt";
	system( "pandoc -s -r html $inputFile -o $outputFile" );
}

@list2 = <tmp_md/*.txt>;
foreach (@list2) {
	my $file_in = $_;
	my $file_out = "classes/" . substr($_, 7, length() - 10) . "md";

	open(my $fd_in, $file_in);
	open(my $fd_out, ">>", $file_out);
	my $id = 0;

	foreach my $line (<$fd_in>) {
		if($line =~ /Detailed Description/) {
			$line = "Class Description\n";
			$id = 1; 
		}
		if($line =~ /Member Function Documentation/ 
				|| $line =~ /Constructor & Destructor Documentation/) {
			$id = 0; 
		}
		if ($id == 1) {
			print $fd_out $line; 
		}
	}
}

@list3 = <classes/*>;
foreach (@list3) {
	if ( -z $_ ) {
		system("rm ". $_); }
}
system( "rm -rf tmp_md" );
system( "mv classes ../../api.wiki/" );
print "End generation.\n";
