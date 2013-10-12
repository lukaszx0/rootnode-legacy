#!/usr/bin/perl
# Nginx reload configuration

use warnings;
use strict;
use FindBin qw($Bin);
use lib "$Bin/..";
use Satan::Vhost;

# Get server id
my $hostname = `hostname`;
chomp $hostname;
my ($server_id) = $hostname =~ /web(\d+)\.rootnode\.net$/;

my $v = Satan::Vhost->new({ server_id => $server_id });
my $output = $v->reload;
die $output if $output;

