#! /bin/sh
#
# Configuration files for Exim4 needed by sympa
# Christian Bayle, Roland Mas, debian-sf (GForge for Debian)
# Converted to Exim4 by Guillem Jover
#
# This will prepare configuration changes needed by sympa. Other
# mailing-list management software may need different exim4
# configuration changes.
#
# The principle is to generate files somewhere in /var/lib that will
# later (in postinst) be applied with ucf on real exim4 configuration
# files (in /etc), by gforge-mta-exim4 package.


set -e

if [ $(id -u) != 0 ] ; then
  echo "You must be root to run this, please enter passwd"
  exec su -c "$0 $1"
fi

####
# Handle the three configuration setups

oldforgename=gforge
forgename=fusionforge
packagename=$forgename-plugin-sympa
# The new files which will later be applied with ucf are generated inside that dir, and not directly in /etc
ucf_new_dir=/var/lib/$forgename/$packagename/etc

cfg_exim4=/etc/exim4/exim4.conf
cfg_exim4_templ=/etc/exim4/exim4.conf.template
#cfg_exim4_split_main=/etc/exim4/conf.d/main/01_exim4-config_listmacrosdefs

cfg_exim4_localmacros=/etc/exim4/exim4.conf.localmacros
cfg_exim4_split_router=/etc/exim4/conf.d/router/450_local-config_sympa_aliases

#cfg_exim4_main="$cfg_exim4_templ $cfg_exim4_split_main"
cfg_exim4_main="$cfg_exim4_templ"
cfg_exim4_router="$cfg_exim4_templ"

if [ -e $cfg_exim4 ]; then
  cfg_exim4_main="$cfg_exim4_main $cfg_exim4"
  cfg_exim4_router="$cfg_exim4_router $cfg_exim4"
fi

# cfg_aliases=/etc/aliases
# cfg_aliases_gforge=$cfg_aliases.gforge-new

pattern=$(basename $0).XXXXXX

case "$1" in
  configure-files)
    # ####
    # # Configure aliases

    # cp -a $cfg_aliases $cfg_aliases_gforge

    # # Redirect "noreply" mail to the bit bucket (if need be)
    # noreply_to_bitbucket=$(perl -e'require "/etc/gforge/local.pl"; print "$noreply_to_bitbucket\n";')
    # if [ "$noreply_to_bitbucket" = "true" ] ; then
    #   if ! grep -q "^noreply:" $cfg_aliases_gforge; then
    # 	echo "### Next line inserted by GForge install" >> $cfg_aliases_gforge
    # 	echo "noreply: :blackhole:" >> $cfg_aliases_gforge
    #   fi
    # fi

    # # Redirect "gforge" mail to the site admin
    # server_admin=$(perl -e'require "/etc/gforge/local.pl"; print "$server_admin\n";')
    # if ! grep -q "^gforge:" $cfg_aliases_gforge; then
    #   echo "### Next line inserted by GForge install" >> $cfg_aliases_gforge
    #   echo "gforge: $server_admin" >> $cfg_aliases_gforge
    # fi

    ####
    # Modify exim4 configurations

    # First, get the list of local domains right

    mkdir -p $ucf_new_dir/exim4

#     for m in $cfg_exim4_main; do
#       #cfg_gforge_main=$m.gforge-new
#       cfg_gforge_main=$ucf_new_dir/exim4/$(basename $m).$packagename-new
#       tmp1=$(mktemp /tmp/$pattern)

#       cp -a $m $cfg_gforge_main

#       perl -e '
# require ("/etc/gforge/local.pl") ;
# $seen_gf_domains = 0;
# while (($l = <>) !~ /^\s*domainlist\s*local_domains/) {
#   print $l;
#   $seen_gf_domains = 1 if ($l =~ /\s*GFORGE_DOMAINS=/);
#   $seen_pg_servers = 1 if ($l =~ m,hide pgsql_servers = .*./var/run/postgresql/.s.PGSQL.5432..*/${sys_dbuser}_mta,);
# };
# print "hide pgsql_servers = (/var/run/postgresql/.s.PGSQL.5432)/mail/Debian-exim/bogus:(/var/run/postgresql/.s.PGSQL.5432)/$sys_dbname/${sys_dbuser}_mta/${sys_dbuser}_mta\n" unless $seen_pg_servers;
# print "GFORGE_DOMAINS=$sys_users_host:$sys_lists_host\n" unless $seen_gf_domains;
# chomp $l;
# $l .= ":GFORGE_DOMAINS" unless ($l =~ /^[^#]*GFORGE_DOMAINS/);
# print "$l\n" ;
# while (<>) { print; };
# ' < $cfg_gforge_main > $tmp1

#       cat $tmp1 > $cfg_gforge_main
#       rm $tmp1
#     done

    # Second, insinuate the support for system pipes

echo > $ucf_new_dir/exim4/$(basename $cfg_exim4_localmacros).$packagename-new  <<EOF
# BEGIN GFORGE BLOCK -- DO NOT EDIT #
# Activating pipe transport in system_aliases router (pipes in /etc/aliases)
.ifndef SYSTEM_ALIASES_PIPE_TRANSPORT
SYSTEM_ALIASES_PIPE_TRANSPORT = address_pipe
.endif
.ifndef SYSTEM_ALIASES_USER
SYSTEM_ALIASES_USER = sympa
.endif
.ifndef SYSTEM_ALIASES_GROUP
SYSTEM_ALIASES_GROUP = sympa
.endif
# END GFORGE BLOCK #
EOF

    # Second, insinuate our forwarding rules in the routers section

    perl -e '
require ("/etc/gforge/local.pl") ;

my $gf_block = "# BEGIN GFORGE BLOCK -- DO NOT EDIT #
# Using alias pipe definitions for the Sympa lists in /etc/mail/sympa/aliases
sympa_aliases:
  debug_print = "R: system_aliases for $local_part@$domain"
  driver = redirect
  domains = +local_domains
  allow_fail
  allow_defer
  data = ${lookup{$local_part}lsearch{/etc/mail/sympa/aliases}}
  user = sympa
  group = sympa
  pipe_transport = address_pipe
# END GFORGE BLOCK #
";

print $gf_block;
' > $cfg_exim4_split_router

    # processing the non-split router definitions
    for r in $cfg_exim4_router; do
      echo Processing $r

      #cfg_gforge_router=$r.gforge-new
      cfg_gforge_router=$ucf_new_dir/exim4/$(basename $r).$packagename-new
      tmp1=$(mktemp /tmp/$pattern)

      cp -a $cfg_gforge_router $tmp1

      perl -e '
$routerfname = shift ;
open ROUTERS, $routerfname || die $!;
my @gf_block = <ROUTERS>;
close ROUTERS;

while (<>) {
  print;
  last if /^\s*begin\s*routers\s*$/;
};
my $in_gf_block = 0;
my $gf_block_done = 0;
my @line_buf = ();
while (<>) {
  last if /^\s*begin\s*$/;
  if (/^# BEGIN GFORGE BLOCK -- DO NOT EDIT #/) {
    $in_gf_block = 1;
    push @line_buf, @gf_block unless $gf_block_done;
    $gf_block_done = 1;
  };
  push @line_buf, $_ unless $in_gf_block;
  $in_gf_block = 0 if /^# END GFORGE BLOCK #/;
};
push @line_buf, $_;
print @gf_block unless $gf_block_done;
print @line_buf;
while (<>) { print; };
' $cfg_exim4_split_router < $tmp1 > $cfg_gforge_router

      rm $tmp1
    done

  ;;

  configure)
    [ -x /usr/bin/newaliases ] && newaliases
    invoke-rc.d exim4 restart
  ;;

  purge-files)
    tmp1=$(mktemp /tmp/$pattern)

    # cp -a $cfg_aliases $cfg_aliases_gforge

    # grep -v "^gforge:" $cfg_aliases_gforge > $tmp1
    # # Redirect "noreply" mail to the bit bucket (if need be)
    # noreply_to_bitbucket=$(perl -e'require "/etc/gforge/local.pl"; print "$noreply_to_bitbucket\n";')
    # if [ "$noreply_to_bitbucket" = "true" ] ; then
    #   grep -v "^noreply:" $tmp1 > $cfg_aliases_gforge
    # else
    #   cat $tmp1 > $cfg_aliases_gforge
    # fi

    # rm -f $tmp1

#     for m in $cfg_exim4_main; do
#       #cfg_gforge_main=$m.gforge-new
#       cfg_gforge_main=$ucf_new_dir/exim4/$(basename $m).$packagename-new
#       tmp1=$(mktemp /tmp/$pattern)

#       cp -a $m $tmp1

#       # First, replace the list of local domains
#       perl -e '
# while (<>) {
#   last if /^\s*domainlist\s*local_domains/;
#   print unless /\s*GFORGE_DOMAINS=/;
# };
# chomp;
# /^(\s*domainlist\s*local_domains\s*=\s*)(\S+)/;
# my $l = $1 . join (":", grep(!/GFORGE_DOMAINS/, (split ":", $2)));
# print "$l\n" ;
# while (<>) { print; };
# ' < $tmp1 > $cfg_gforge_main

#       rm $tmp1
#     done

    if [ -f $cfg_exim4_split_router ]
    then
    	mv $cfg_exim4_split_router $cfg_exim4_split_router.gforge-new
    fi

    for r in $cfg_exim4_router; do
      #cfg_gforge_router=$r.gforge-new
      cfg_gforge_router=$ucf_new_dir/exim4/$(basename $r).$packagename-new
      tmp1=$(mktemp /tmp/$pattern)

      cp -a $cfg_gforge_router $tmp1

      # Second, kill our forwarding rules
      perl -e '
while (<>) {
  print;
  last if /^\s*begin\s*routers\s*$/;
};
my $in_gf_block = 0;
while (<>) {
  last if /^\s*begin\s*$/;
  $in_gf_block = 1 if /^# BEGIN GFORGE BLOCK -- DO NOT EDIT #/;
  print unless $in_gf_block;
  $in_gf_block = 0 if /^# END GFORGE BLOCK #/;
};
print;
while (<>) { print; };
' < $tmp1 > $cfg_gforge_router

      rm $tmp1
    done
  ;;

  purge)
  ;;

  *)
    echo "Usage: $0 {configure|configure-files|purge|purge-files}"
    exit 1
  ;;

esac

