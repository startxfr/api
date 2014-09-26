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
	@new_list = glob "'${dir}/*'";
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
	my (@list) = glob "'${dir}*'";
	return rec(@list);
}

sub cleanList {
	my (@list) = @_;
	my (@newList);
	foreach (@list) {
		if ($_ =~ /class/) {
			push(@newList, $_);
		}
	}
	return @newList;
}

### MAIN ###
print "Begin script\n";
my ($dir) = "../api-lib/lib/resources/";

## GET ALL FILES FROM DIR (RECURSIVELY) ##
@list = getAllFiles($dir);
@list2 = cleanList(@list);
foreach (@list2) {
	print $_ . "\n";
}
print $#list2 + 1 . "\n";


print "End script\n";
exit 1;
