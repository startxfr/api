#!/usr/bin/perl

use warnings;
use Term::ANSIColor qw(:constants);

# DEBUG SUBROUTINE : PRINT STRING IN RED
sub debug {
	my ($str) = $_[0];
	print RED, $str, RESET, "\n";
}

sub debugl {
	my (@list) = @_;
	foreach (@list) {
		debug $_;
	}
}

sub split_dir {
	my ($dir) = $_[0];
	@new_list = glob "${dir}/*";
	return @new_list;
}

sub rec {
	my (@list) = @_;
	my (@new_list);
	my (@list2);
	my ($i) = 0;
	foreach (@list) {
		if ( -d $list[$i] ) {
			@list2 = split_dir($list[$i]);
			@list2 = rec(@list2);
			push(@new_list, @list2);
		}
		else {
			push(@new_list, $list[$i]);
		}
		$i++;
	}
	return @new_list;
}

sub getAllFiles {
	my ($dir) = $_[0];
	my (@list) = glob "${dir}*";
	return rec(@list);
}

sub cleanList {
	my (@list) = @_;
	my (@newList);
	foreach (@list) {
		if ($_ =~ /Resource.php/) {
			push(@newList, $_);
		}
	}
	return @newList;
}

sub sanitize {
	my $str = $_[0];
	my @tab = split(/\//, $str);
	my ($file_name) = pop @tab;
	my ($sub_dir) = join "/", "resources", @tab[4..$#tab];
	system("mkdir -p $sub_dir");
	my ($return) = join "/", $sub_dir, $file_name;
	return $return;
}

sub writeThemFiles {
	my $file = $_[0];
	my $fileOut = sanitize $file;

	open my $fdIn, $file or die "OPEN ERROR ON FILE $file: $!";
	open my $fdOut, ">>", $fileOut or die "OPEN ERROR ON FILE $fileOut: $!";

	foreach $line (<$fdIn>) {
print $line . "\n";
		if ($line =~ /\Q\/**\E/) {
			while ($line !=~ /\Q*\/\E/) {
				print $fileOut $line;
			}
		}
		if ($line =~ /^class/) {
			last;
		}
	}
	close $fdIn;
	close $fdOut;
}

### MAIN ###
print "Begin script\n";
my ($dir) = "../../../../api-lib/lib/resources/";

## GET ALL FILES FROM DIR (RECURSIVELY) ##
@list = getAllFiles($dir);
@list2 = cleanList(@list);
writeThemFiles $list2[0];
exit;
foreach (@list2) {
	writeThemFiles $_;
	#print $_ . "\n";
}
print "size: " . ($#list2 + 1) . "\n";

print "End script\n";
