#!/usr/bin/perl
#
# Website generator
# Rootnode, http://rootnode.net
#
# Copyright (C) 2012 Marcin Hlybin
# All rights reserved.
#

use warnings;
use strict;
use File::Path qw(make_path);
use Readonly;
use Data::Dumper;
use Smart::Comments;

Readonly my $SOURCE_DIR => '/home/rootnode/git/pages/rootnode.net';
Readonly my $DEST_DIR   => '/home/rootnode/web/rootnode.net/htdocs';

-d $SOURCE_DIR or die "\$SOURCE_DIR ($SOURCE_DIR) not found.\n";
-d $DEST_DIR   or die "\$DEST_DIR ($DEST_DIR) not found.\n";

# Fetch includes
my %inc;
foreach my $type (qw(header footer)) {
	my $file = "$SOURCE_DIR/includes/$type";
	$inc{$type} = do { local( @ARGV, $/ ) = $file; <> };
}

# Fetch pages
my %page;
my @files = `find $SOURCE_DIR/pages -type f`;
foreach my $file_path (@files) {
	chomp $file_path;
	my $file_name = $file_path;
	   $file_name =~ s/^\Q$SOURCE_DIR\/pages\/\E//;
	$page{$file_name} = do { local( @ARGV, $/ ) = $file_path; <> };
}

# Generate pages
foreach my $page_name (keys %page) {
	print "Generating $page_name...\n";

	# Create destination path
	if ($page_name =~ /\//) {
		my ($dir_part, $file_part) = split /\//, $page_name;
		make_path("$DEST_DIR/$dir_part");	
	}

	# Include files
	my $header = $inc{header};
	my $footer = $inc{footer};
	
	# Save file
	open my $fh, '>', "$DEST_DIR/$page_name";
	print $fh $header, $page{$page_name}, $footer;
	close $fh;
}

# Copy static pages
print "Copying static pages...\n";
system("cp -pr $SOURCE_DIR/static/* $DEST_DIR/");
