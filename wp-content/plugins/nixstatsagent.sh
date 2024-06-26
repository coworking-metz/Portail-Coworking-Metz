#!/bin/bash

set -o nounset  # Treat unset variables as an error

export LC_ALL=C

logfile="nixstatsagent.log"

logtoken=${2:-notset}

#: Check root privilege :#
if [ "$(id -u)" != "0" ];
then
   echo "error: Installer needs root permission to run, please run as root."
   exit 1
fi

touch /var/log/nixstatsagent.log


pathfix () {
        if ! echo "$PATH" | /bin/grep -Eq "(^|:)$1($|:)" ; then
           if [ "$2" = "after" ] ; then
              PATH="$PATH:$1"
           else
              PATH="$1:$PATH"
           fi
        fi
}

pathfix /usr/local/bin/ after

__Requirements(){
   cat<<REQUIREMENTS

   An error occured, please check the install log file (nixstatsagent.log)!

REQUIREMENTS
}


__Norce(){
   cat<<Notic

   Notic: Userid argument missing
   Example: bash nixstatsagent.sh userid

Notic
}

Linux_Release(){

   FORCE_OS="defaultv"

   for i in $ARGUMENTS;
   do
       case $i in
           --os=*)
           FORCE_OS="${i#*=}"
           shift # past argument=value
           ;;
       esac
   done

   if [[ "$FORCE_OS" != "defaultv" ]]; then
      echo "$FORCE_OS"
   elif [ -e '/etc/os-release' ] || [ -e '/etc/redhat-release' ] || [ -e 'lsb_release' ] || [ -e '/etc/debian-release' ]; then
      Debian=$( cat /etc/*-release | grep -o debian | head -1 )
      Ubuntu=$( cat /etc/*-release | grep -o ubuntu | head -1 )
      CentOS=$( cat /etc/*-release | grep -io centos| head -1 )
      Sangoma=$( cat /etc/*-release | grep -io sangoma| head -1 )
      Scientific=$( cat /etc/*-release | grep -io scientific| head -1 )
      Oracle=$( cat /etc/*-release | grep -io Oracle| head -1 )
      Fedora=$( cat /etc/*-release | grep -o fedora | head -1 )
      CloudLinux=$( cat /etc/*-release | grep -io cloudlinux| head -1 )
      Freepbx=$( cat /etc/*-release | grep -io SHMZ | head -1 )
      Amazon=$( cat /etc/*-release | grep -o amazon | head -1 )

      if [ -e 'lsb_release' ]; then
          Debian=$( lsb_release -ds | grep -io debian | head -1 | tr '[:upper:]' '[:lower:]')
          Ubuntu=$( lsb_release -ds | grep -io ubuntu | head -1 | tr '[:upper:]' '[:lower:]')
          CentOS=$( lsb_release -ds | grep -io centos| head -1 | tr '[:upper:]' '[:lower:]')
          Fedora=$( lsb_release -ds | grep -io fedora | head -1 | tr '[:upper:]' '[:lower:]')
      fi

      if [ "$Ubuntu" == 'ubuntu' ]; then
         echo "Ubuntu"
      elif [ "$Debian" == 'debian' ]; then
         echo "Debian"
      elif [ "$Oracle" == 'Oracle' ]; then
         echo "CentOS"
      elif [ "$Sangoma" == 'Sangoma' ]; then
         echo "CentOS"
      elif [ "$CentOS" == 'centos' ]; then
         echo "CentOS"
      elif [ "$CentOS" == 'CentOS' ]; then
         echo "CentOS"
      elif [ "$Freepbx" == 'SHMZ' ]; then
         echo "CentOS"
      elif [ "$Scientific" == 'Scientific' ]; then
         echo "CentOS"
      elif [ "$CloudLinux" == 'CloudLinux' ]; then
         echo "CentOS"
      elif [ "$Amazon" == 'amazon' ]; then
         echo "Amazon"
      elif [ "$Fedora" == 'fedora' ]; then
         echo "CentOS"
      else
         __Requirements
      fi
   elif [ "$(uname)" == 'FreeBSD' ]; then
      echo "FreeBSD"
   fi
}

ARGUMENTS="$@"

Linux_Version(){

   install "$(get_installer)" less
   FORCE_VERSION="defaultv"

   for i in $ARGUMENTS;
   do
       case $i in
           --osversion=*)
           FORCE_VERSION="${i#*=}"
           shift # past argument=value
           ;;
       esac
   done

   if [[ "$FORCE_VERSION" != "defaultv" ]]; then
      echo "$FORCE_VERSION"
   elif command -v lsb_release >/dev/null 2>&1; then
      VERSION_ID=$(lsb_release -r | sed 's/[\t: a-z A-Z()]//g' | head -1 | cut -d "." -f1)
      VERSION=${VERSION_ID%.*}
      echo "$VERSION"
   elif [ -e "/etc/os-release" ]; then
      VERSION_ID=$(less /etc/os-release | grep VERSION_ID | head -1 | sed 's/VERSION_ID=//' | sed 's/"//' | sed 's/"//')
      VERSION=${VERSION_ID%.*}
      echo "$VERSION"
   elif [ -e "/etc/centos-release" ]; then
      CentOS_ID=$(less /etc/centos-release | sed 's/[a-z A-Z()]//g' | head -1)
      CentOS_VERSION=${CentOS_ID%.*}
      echo "$CentOS_VERSION"
   elif [ -e "/etc/system-release" ]; then
      CentOS_ID=$(less /etc/system-release | sed 's/[a-z A-Z()]//g' | head -1)
      CentOS_VERSION=${CentOS_ID%.*}

      echo "$CentOS_VERSION"
   elif [ -e "/etc/redhat-release" ]; then
      CentOS_ID=$(less /etc/redhat-release | sed 's/[a-z A-Z()]//g' | head -1)
      CentOS_VERSION=${CentOS_ID%.*}

      echo "$CentOS_VERSION"
   elif [ -e "/etc/debian-release" ]; then
      Debian_ID=$(less /etc/debian-release | sed 's/[a-z A-Z()]//g' | head -1)
      Debian_ID=${VERSION_ID%.*}

      echo "$Debian_ID"
  fi

}

#: Function for install programes :#
install () {
   installer="$1"
   program="$2"
   "$installer" install -y "$program" >> $logfile 2>&1
   rc=$?
   if [ "$rc" != "0" ]; then
        echo Installer exited with error code $?. See $logfile for details.
        exit
   fi
 }

#: Get installer for courrect platform :#
get_installer () {
   case $(Linux_Release) in
      Debian*)
         apt-get update >> $logfile 2>&1
         rc=$?
         if [ "$rc" != "0" ]; then
                echo apt-get upgrade returned error code $rc. Please see $logfile for details.
                exit
         fi
         echo "apt-get";;
      Ubuntu*)
         apt-get update >> $logfile 2>&1
         rc=$?
         if [ "$rc" != "0" ]; then
                echo apt-get upgrade returned error code $rc. Please see $logfile for details.
                exit
         fi
         echo "apt-get";;
      CentOS*)
         echo "yum";;
      Fedora*)
         echo "yum";;
      Amazon*)
         echo "yum";;
      FreeBSD*)
         echo "pkg";;
   esac
}

ensure_PIP(){
   if [ -e '/usr/bin/pip' ] || [ -e '/usr/bin/pip3' ] || [ -e '/usr/bin/easy_install' ]; then
      echo "installed"
   else
      echo "pip is not installed"
   fi
}


ensure_nixstatsagent(){
   if [ -e "$( command which nixstatsagent)" ]; then
      echo "installed"
   else
      echo "nixstatsagent is not installed"
   fi
}

Service_Name="nixstatsagent"
setupsystemd(){


   if [ -e "$( command which nixstatsagent)" ]; then
      nixstatsagent_path="$(which nixstatsagent)"
   else
      nixstatsagent_path="/usr/local/bin/nixstatsagent"
   fi

      echo "Creating and starting service"
cat << EOF > /etc/systemd/system/nixstatsagent.service
[Unit]
Description=Nixstatsagent

[Service]
ExecStart=$nixstatsagent_path
User=nixstats

[Install]
WantedBy=multi-user.target
EOF
    if test -x /usr/bin/nixstatsagent && ! test -x /usr/local/bin/nixstatsagent; then
        ln -s /usr/bin/nixstatsagent /usr/local/bin
    fi
      command chmod 644 /etc/systemd/system/nixstatsagent.service
      command systemctl daemon-reload; systemctl enable nixstatsagent; systemctl start nixstatsagent
      echo "Created the nixstatsagent service"
}
setupchkconfig(){
nixstatsagent_path="$(which nixstatsagent)"
cat << EOF > "/etc/init.d/nixstatsagent"
#!/bin/sh
#       /etc/rc.d/init.d/nixstatsagent
#       Init script for nixstatsagent
# chkconfig:   2345 20 80
# description: Init script for nixstats monitoring agent

### BEGIN INIT INFO
# Provides:       daemon
# Required-Start: \$rsyslog
# Required-Stop:
# Should-Start:
# Should-Stop:
# Default-Start: 2 3 4 5
# Default-Stop:  0 1 6
# Short-Description: nixstats monitoring agent
# Description: nixstats monitoring agent
### END INIT INFO

# Source function library.
. /etc/rc.d/init.d/functions

prog=nixstatsagent
app=$nixstatsagent_path
pid_file=/var/run/nixstatsagent.pid
lock_file=/var/lock/subsys/nixstatsagent
proguser=nixstats

[ -e /etc/sysconfig/\$prog ] && . /etc/sysconfig/\$prog

start() {
    [ -x \$exec ] || exit 5
    echo -n \$"Starting \$prog: "
    daemon --user \$proguser --pidfile \$pid_file "nohup \$app >/dev/null 2>&1 &"
    RETVAL=\$?
    [ \$RETVAL -eq 0 ] && touch \$lock_file
    echo
    return \$RETVAL
}

stop() {
    echo -n \$"Stopping \$prog: "
    killproc \$prog
    RETVAL=\$?
    echo
    [ \$RETVAL -eq 0 ] && rm -f \$lock_file
    return \$RETVAL
}

restart() {
    stop
    start
}

rh_status() {
    status \$prog
}

rh_status_q() {
    rh_status >/dev/null 2>&1
}

case "\$1" in
    start)
        rh_status_q && exit 0
        \$1
        ;;
    stop)
        rh_status_q || exit 0
        \$1
        ;;
    restart)
        \$1
        ;;
    status)
        rh_status
        ;;
    condrestart|try-restart)
        rh_status_q || exit 0
        restart
        ;;
    *)
        echo \$"Usage: \$0 {start|stop|status|restart}"
        exit 2
esac
exit \$?
EOF
 chmod +x /etc/init.d/nixstatsagent
 command chkconfig --add nixstatsagent
 command chkconfig nixstatsagent on
 command service nixstatsagent start

}
setupbsd(){
nixstatsagent_path="$(which nixstatsagent)"
      echo
      echo "Creating and starting service"
      echo
cat << EOF > "/etc/rc.d/nixstatsagent"
#!/bin/sh
#
# PROVIDE: nixstats
# REQUIRE: networking
# KEYWORD: shutdown

. /etc/rc.subr

name="nixstatsagent"
rcvar="\${name}_enable"

load_rc_config \$name
: \${nixstatsagent_enable:=no}
: \${nixstats_bin_path="/usr/local/bin/nixstatsagent"}
: \${nixstats_run_user="nixstats"}

pidfile="/var/run/nixstatsagent.pid"
logfile="/var/log/nixstatsagent.log"

command="\${nixstats_bin_path}"

start_cmd="nixstats_start"
status_cmd="nixstats_status"
stop_cmd="nixstats_stop"

nixstats_start() {
    echo "Starting \${name}..."
    /usr/sbin/daemon -u \${nixstats_run_user} -c -p \${pidfile} -f \${command}
}

nixstats_status() {
    if [ -f \${pidfile} ]; then
       echo "\${name} is running as \$(cat \$pidfile)."
    else
       echo "\${name} is not running."
       return 1
    fi
}

nixstats_stop() {
    if [ ! -f \${pidfile} ]; then
      echo "\${name} is not running."
      return 1
    fi

    echo -n "Stopping \${name}..."
    kill -KILL \$(cat \$pidfile) 2> /dev/null && echo "stopped"
    rm -f \${pidfile}
}

run_rc_command "\$1"
EOF
      command chmod +x /etc/rc.d/nixstatsagent

      command echo $'\n'"nixstatsagent_enable=\"YES\"" >> /etc/rc.conf
      command service nixstatsagent start
      echo
      echo "Service is created. Service Name is nixstatsagent"
      echo
}


amazonlinux() {
nixstatsagent_path="$(which nixstatsagent)"

if  [ -x "$(command -v systemctl)" ]; then
cat << EOF > /etc/systemd/system/nixstatsagent.service
[Unit]
Description=Nixstatsagent

[Service]
ExecStart=$nixstatsagent_path
User=nixstats

[Install]
WantedBy=multi-user.target
EOF
        systemctl daemon-reload
        systemctl start nixstatsagent
else
        wget -O /etc/init.d/nixstatsagent https://www.nixstats.com/scripts/init/amazonlinux.txt
        chmod 755 /etc/init.d/nixstatsagent
        service nixstatsagent start
        chkconfig nixstatsagent on
fi

}

setupinitd() {
nixstatsagent_path="$(which nixstatsagent)"
cat << EOF > /etc/init.d/nixstatsagent
#!/bin/bash

### BEGIN INIT INFO
# Provides:          nixstatsagent
# Required-Start:    \$remote_fs \$syslog
# Required-Stop:     \$remote_fs \$syslog
# Should-Start:      \$network \$named \$time
# Should-Stop:       \$network \$named \$time
# Default-Start:     2 3 4 5
# Default-Stop:      0 1 6
# Short-Description: Start and stop the nixstatsagent daemon
# Description:       Controls the nixstats monitoring daemon nixstatsagent
### END INIT INFO

. /lib/lsb/init-functions

PATH=/sbin:/bin:/usr/sbin:/usr/bin:/usr/local/bin
DAEMON=$nixstatsagent_path
NAME=nixstatsagent
DESC=nixstatsagent
PIDFILE=/var/run/nixstatsagent.pid

test -x \$DAEMON || exit 0
set -e

function _start() {
    start-stop-daemon --start --quiet --name \$NAME --oknodo --pidfile \$PIDFILE --chuid nixstats --background --make-pidfile --startas \$DAEMON
}

function _stop() {
    start-stop-daemon --stop --quiet --name \$NAME --pidfile \$PIDFILE --oknodo --retry 3
    rm -f \$PIDFILE
}

function _status() {
    start-stop-daemon --status --quiet --pidfile \$PIDFILE
    return \$?
}

case "\$1" in
        start)
                echo -n "Starting \$DESC: "
                _start
                echo "ok"
                ;;
        stop)
                echo -n "Stopping \$DESC: "
                _stop
                echo "ok"
                ;;
        restart|force-reload)
                echo -n "Restarting \$DESC: "
                _stop
                sleep 1
                _start
                echo "ok"
                ;;
        status)
                echo -n "Status of \$DESC: "
                _status && echo "running" || echo "stopped"
                ;;
        *)
                N=/etc/init.d/\$NAME
                echo "Usage: \$N {start|stop|restart|force-reload|status}" >&2
                exit 1
                ;;
esac

exit 0
EOF
chmod 755 /etc/init.d/nixstatsagent
update-rc.d nixstatsagent defaults
service nixstatsagent start

if [ -x "$nixstatsagent_path" ];
then
   echo "nixstatsagent succesfully started and is running."
else
   echo "nixstatsagent failed to start, check check nixstatsagent.log for debug information."
   exit 1
fi
}
systemd(){
   if [ "$(uname)" == 'FreeBSD' ]; then
    setupbsd
   elif which systemctl > /dev/null 2>&1; then
        setupsystemd
   elif [ "$(Linux_Release)" == 'CentOS' ] && ! which systemctl > /dev/null 2>&1; then
    setupchkconfig
   else
    setupinitd
   fi

    if [ "$(ensure_nixstatsagent)" == 'installed' ]; then

        if [ -f /etc/nixstats/token ]; then

            command crontab -u nixstats -l | grep -v 'nixstats'  | crontab -u nixstats -
            command rm -rf /etc/nixstats/
        fi

        if [ -f /opt/nixstats/nixstats.py ]; then

            command /etc/init.d/nixstats stop
            command pkill -f nixstats.py

            if [ -n "$(command -v chkconfig)" ]
            then
                command chkconfig --del nixstats
            fi

            if [ -n "$(command -v update-rc.d)" ]
            then
                command update-rc.d -f nixstats remove
            fi

            command rm -f /etc/init.d/nixstats
            command rm -rf /opt/nixstats/

        fi

    fi

}

if [ ! -f /etc/nixstats.ini ]; then
    wget -qO /etc/nixstats.ini https://www.nixstats.com/nixstats.ini
fi
case $(Linux_Release) in
   Debian*)
      if [ "$(Linux_Version)" -ge 6 ]; then
         if [ $# -lt 1 ]; then
            echo "NIXStats userid missing from the installer."
            __Norce
            exit 1
            #else
         fi

        id nixstats &>/dev/null
        if [[ $? -ne 0 ]]; then
                useradd --system --user-group --key USERGROUPS_ENAB=yes -M nixstats --shell /bin/false
                chown nixstats /var/log/nixstatsagent.log
        fi

         if [ "$(ensure_PIP)" != 'installed' ]  || ! which pip >/dev/null 2>&1; then
            echo "Installing ..."

           if [ "$(Linux_Version)" -ge 9 ]; then
               echo "Installing python3-pip ..."
               install "$(get_installer)" python3-dev
               install "$(get_installer)" python3-setuptools
               install "$(get_installer)" gcc
               install "$(get_installer)" python3-pip --ignore-missing
           else
                echo "Installing python2-pip ..."
                install "$(get_installer)" python-dev
                install "$(get_installer)" libffi-dev
                install "$(get_installer)" libssl-dev
                install "$(get_installer)" python-setuptools
                install "$(get_installer)" gcc
                install "$(get_installer)" libevent-dev
                install "$(get_installer)" python3-pip --ignore-missing
                hash -r

                echo "Installing nixstatsagent ... "
                command  pip install --upgrade pip >>$logfile 2>&1
                rc=$?
                    if [ "$rc" != "0" ]; then
                    echo pip install/upgrade returned error $?. Please see $logfile for details.
                    exit
                fi
                command  pip install nixstatsagent --upgrade >>$logfile 2>&1
                rc=$?
                if [ "$rc" != "0" ] && [ "$rc" != "127" ]; then
                    # 127 is warning probably on urllib3
                    command  pip2.7 install nixstatsagent --upgrade >>$logfile 2>&1

                    rca=$?
                    if [ "$rca" != "0" ] && [ "$rca" != "127" ]; then
                        echo pip install of nixstatsagent returned error $?. Please see $logfile for details.
                        exit
                    fi
                fi
            fi
         else
            install "$(get_installer)" python3-dev
            install "$(get_installer)" libffi-dev
            install "$(get_installer)" libssl-dev
            install "$(get_installer)" python3-setuptools
            install "$(get_installer)" libevent-dev
            install "$(get_installer)" gcc
            install "$(get_installer)" python3-pip --ignore-missing
            hash -r
         fi

         if [ "$(ensure_nixstatsagent)" != 'installed' ]; then
            echo "Installing nixstatsagent ... "
           if [ "$(Linux_Version)" -ge 9 ]; then
               echo "Installing nixstatsagent ... "
               command  pip3 install nixstatsagent --upgrade >>$logfile 2>&1
               rc=$?
               if [ "$rc" != "0" ] && [ "$rc" != "127" ]; then
                   echo pip install of nixstatsagent returned error $?. Please see $logfile for details.
                   exit
               fi
           else
                install "$(get_installer)" python-dev
                install "$(get_installer)" libffi-dev
                install "$(get_installer)" libssl-dev
                install "$(get_installer)" python-setuptools
                install "$(get_installer)" gcc
                install "$(get_installer)" libevent-dev
                install "$(get_installer)" python3-pip
                command  pip install nixstatsagent --upgrade >>$logfile 2>&1
                rc=$?
                if [ "$rc" != "0" ] && [ "$rc" != "127" ]; then
                    # 127 is warning probably on urllib3
                    command  pip2.7 install nixstatsagent --upgrade >>$logfile 2>&1

                    rca=$?
                    if [ "$rca" != "0" ] && [ "$rca" != "127" ]; then
                        echo pip install of nixstatsagent returned error $?. Please see $logfile for details.
                        exit
                    fi
                fi
                command  pip install --upgrade pip >> $logfile 2>&1
                rc=$?
                    if [ "$rc" != "0" ]; then
                    echo pip install/upgrade returned error $?. Please see $logfile for details.
                    exit
                fi
            fi

            echo "Generation a server id ..."

            if [ ! -f /etc/nixstats-token.ini ]; then
                command nixstatshello "$1" /etc/nixstats-token.ini
            fi
            systemd "$1"

         else
               hash -r
               echo "Upgrading nixstatsagent"
              if [ "$(Linux_Version)" -ge 9 ]; then
                  echo "Installing python3-pip ..."
                  install "$(get_installer)" python3-dev
                  install "$(get_installer)" python3-setuptools
                  install "$(get_installer)" gcc
                  install "$(get_installer)" python3-pip --ignore-missing
                  echo "Installing nixstatsagent ... "
                  command  pip3 install nixstatsagent --upgrade >>$logfile 2>&1
                  rc=$?
                  if [ "$rc" != "0" ] && [ "$rc" != "127" ]; then
                      echo pip install of nixstatsagent returned error $?. Please see $logfile for details.
                      exit
                  fi
              else
                    install "$(get_installer)" python-dev
                    install "$(get_installer)" libffi-dev
                    install "$(get_installer)" libssl-dev
                    install "$(get_installer)" python-setuptools
                    install "$(get_installer)" gcc
                    install "$(get_installer)" libevent-dev
                    install "$(get_installer)" python3-pip --ignore-missing
                   command  pip install nixstatsagent --upgrade >>$logfile 2>&1
                   rc=$?
                   if [ "$rc" != "0" ] && [ "$rc" != "127" ]; then
                       # 127 is warning probably on urllib3
                       command  pip2.7 install nixstatsagent --upgrade >>$logfile 2>&1

                       rca=$?
                       if [ "$rca" != "0" ] && [ "$rca" != "127" ]; then
                           echo pip install of nixstatsagent returned error $?. Please see $logfile for details.
                           exit
                       fi
                   fi
               fi
               if [ ! -f /etc/nixstats-token.ini ]; then
                   command nixstatshello "$1" /etc/nixstats-token.ini
               fi
               systemd "$1"
         fi
        if [ "${#logtoken}" -eq 24 ] && [ -f "/etc/nixstats-token.ini" ]  && [ ! -f "/etc/rsyslog.d/31-nixstats.conf" ]; then
            echo "Enabling logging..."
            install "$(get_installer)" rsyslog-gnutls
            if [ ! -d "/var/spool/rsyslog" ]; then
                 mkdir -p /var/spool/rsyslog
            fi
            if [ ! -d "/etc/rsyslog.d/keys" ]; then
                mkdir -p /etc/rsyslog.d/keys
           fi

            wget -q -O /etc/rsyslog.d/keys/nixstats.ca https://nixstats.com/nixstats.ca
            configfile="
         ##########################################################
         ### Rsyslog Template for Nixstats ###
         ##########################################################

         \$DefaultNetstreamDriverCAFile /etc/rsyslog.d/keys/nixstats.ca
         \$ActionSendStreamDriver gtls # use gtls netstream driver
         \$ActionSendStreamDriverMode 1 # require TLS
         \$ActionSendStreamDriverAuthMode x509/name # authenticate by hostname
         \$ActionSendStreamDriverPermittedPeer log.nixstats.com

         \$template NixFormat,\"<%pri%>%protocol-version% %timestamp:::date-rfc3339% %HOSTNAME% %app-name% %procid% %msgid% [`tail -c 25 /etc/nixstats-token.ini`@$logtoken tag=\\\"rsyslog\\\"] %msg%\\n\"

         # Setup disk assisted queues
         \$WorkDirectory /var/spool/rsyslog # where to place spool files
         \$ActionQueueFileName fwdRule1     # unique name prefix for spool files
         \$ActionQueueMaxDiskSpace 1g       # 1gb queue space
         \$ActionQueueSaveOnShutdown on     # save messages to disk on shutdown
         \$ActionQueueType LinkedList       # run asynchronously
         \$ActionResumeRetryCount -1        # infinite retries if host is down

         *.* @@log.nixstats.com:10514;NixFormat

         #################END CONFIG FILE#########################
                         "
                         cat <<< "$configfile" > /etc/rsyslog.d/31-nixstats.conf
                         command service rsyslog restart
         fi
      else
         __Requirements
      fi;;

   Ubuntu*)
      if [ $# -lt 1 ]; then
         echo "NIXStats userid missing from runner command."
         __Norce
         exit 1
      else

        id nixstats &>/dev/null
        if [[ $? -ne 0 ]]; then
                useradd --system --user-group --key USERGROUPS_ENAB=yes -M nixstats --shell /bin/false
                chown nixstats /var/log/nixstatsagent.log
        fi

         if [ "$(Linux_Version)" -ge 12 ]; then
            if [ "$(ensure_PIP)" != 'installed' ]; then
               echo "Found Ubuntu ..."
               echo "Installing ..."
               echo "Installing Python2-PIP ..."
               if [ "$(Linux_Version)" -ge 16 ]; then
                   install "$(get_installer)"  python3-dev
                   install "$(get_installer)"  python3-setuptools
                   install "$(get_installer)"  gcc
                   install "$(get_installer)"  python3-pip
                   echo "Installing nixstatsagent ... "
                   command  pip3 install nixstatsagent --upgrade >>$logfile 2>&1
                   rc=$?
                   if [ "$rc" != "0" ] && [ "$rc" != "127" ]; then
                       echo pip install of nixstatsagent returned error $?. Please see $logfile for details.
                       exit
                   fi
               else
                   install "$(get_installer)"  python-dev
                   install "$(get_installer)"  python-setuptools
                   install "$(get_installer)"  gcc
                   install "$(get_installer)"  python-pip
                   echo "Installing nixstatsagent ... "
                   command  pip install --upgrade pip urllib3 >> $logfile 2>&1
                   command  pip install nixstatsagent --upgrade >> $logfile 2>&1
                   rc=$?
                   if [ "$rc" != "0" ] && [ "$rc" != "127" ]; then
                       # 127 is warning probably on urllib3
                       if which pip2 >/dev/null; then
                           command  pip2 install nixstatsagent --upgrade >>$logfile 2>&1
                           rcb=$?
                       else
                           command  pip2.7 install nixstatsagent --upgrade >>$logfile 2>&1
                           rcb=$?
                       fi

                       if [ "$rcb" != "0" ] && [ "$rcb" != "127" ]; then
                           echo pip install of nixstatsagent returned error $?. Please see $logfile for details.
                           exit
                       fi
                   fi
               fi


               echo "Generation a server id ..."
               if [ ! -f /etc/nixstats-token.ini ]; then
                   command nixstatshello "$1" /etc/nixstats-token.ini
               fi
               systemd "$1"
            elif [ "$(ensure_nixstatsagent)" != 'installed' ]; then
               echo "Installing nixstatsagent ... "
               if [ "$(Linux_Version)" -ge 16 ]; then
                   install "$(get_installer)"  python3-dev
                   install "$(get_installer)"  python3-setuptools
                   install "$(get_installer)"  gcc
                   install "$(get_installer)"  python3-pip
                   echo "Installing nixstatsagent ... "
                   command  pip3 install nixstatsagent --upgrade >>$logfile 2>&1
                   rc=$?
                   if [ "$rc" != "0" ] && [ "$rc" != "127" ]; then
                       echo pip install of nixstatsagent returned error $?. Please see $logfile for details.
                       exit
                   fi
               else
                   install "$(get_installer)"  python-dev
                   install "$(get_installer)"  python-setuptools
                   install "$(get_installer)"  gcc
                   install "$(get_installer)"  python-pip
                   echo "Installing nixstatsagent ... "
                   command  pip install --upgrade pip urllib3 >> $logfile 2>&1
                   command  pip install nixstatsagent --upgrade >> $logfile 2>&1
                   rc=$?
                   if [ "$rc" != "0" ] && [ "$rc" != "127" ]; then
                       # 127 is warning probably on urllib3
                       if which pip2 >/dev/null; then
                           command  pip2 install nixstatsagent --upgrade >>$logfile 2>&1
                           rcb=$?
                       else
                           command  pip2.7 install nixstatsagent --upgrade >>$logfile 2>&1
                           rcb=$?
                       fi

                       if [ "$rcb" != "0" ] && [ "$rcb" != "127" ]; then
                           echo pip install of nixstatsagent returned error $?. Please see $logfile for details.
                           exit
                       fi
                   fi
               fi

               echo "Generation a server id ..."
               if [ ! -f /etc/nixstats-token.ini ]; then
                   command nixstatshello "$1" /etc/nixstats-token.ini
               fi
               systemd "$1"

            else
               echo "Upgrading nixstatsagent"

               if [ "$(Linux_Version)" -ge 16 ]; then
                   install "$(get_installer)"  python3-dev
                   install "$(get_installer)"  python3-setuptools
                   install "$(get_installer)"  gcc
                   install "$(get_installer)"  python3-pip
                   echo "Installing nixstatsagent ... "
                   command  pip3 install nixstatsagent --upgrade >>$logfile 2>&1
                   rc=$?
                   if [ "$rc" != "0" ] && [ "$rc" != "127" ]; then
                       echo pip install of nixstatsagent returned error $?. Please see $logfile for details.
                       exit
                   fi
               else
                   install "$(get_installer)"  python-dev
                   install "$(get_installer)"  python-setuptools
                   install "$(get_installer)"  gcc
                   install "$(get_installer)"  python-pip
                   echo "Installing nixstatsagent ... "
                   command  pip install --upgrade pip urllib3 >> $logfile 2>&1
                   command  pip install nixstatsagent --upgrade >> $logfile 2>&1
                   rc=$?
                   if [ "$rc" != "0" ] && [ "$rc" != "127" ]; then
                       # 127 is warning probably on urllib3
                       if which pip2 >/dev/null; then
                           command  pip2 install nixstatsagent --upgrade >>$logfile 2>&1
                           rcb=$?
                       else
                           command  pip2.7 install nixstatsagent --upgrade >>$logfile 2>&1
                           rcb=$?
                       fi

                       if [ "$rcb" != "0" ] && [ "$rcb" != "127" ]; then
                           echo pip install of nixstatsagent returned error $?. Please see $logfile for details.
                           exit
                       fi
                   fi
               fi
               if [ ! -f /etc/nixstats-token.ini ]; then
                   command nixstatshello "$1" /etc/nixstats-token.ini
               fi
               systemd "$1"
            fi

            if [ "${#logtoken}" -eq 24 ] && [ -f "/etc/nixstats-token.ini" ]  && [ ! -f "/etc/rsyslog.d/31-nixstats.conf" ]; then
               echo "Enabling logging..."
               install "$(get_installer)" rsyslog-gnutls
               if [ ! -d "/var/spool/rsyslog" ]; then
                    mkdir -p /var/spool/rsyslog
               fi
               if [ ! -d "/etc/rsyslog.d/keys" ]; then
                   mkdir -p /etc/rsyslog.d/keys
               fi

               wget -q -O /etc/rsyslog.d/keys/nixstats.ca https://nixstats.com/nixstats.ca
               configfile="
##########################################################
### Rsyslog Template for Nixstats ###
##########################################################

\$DefaultNetstreamDriverCAFile /etc/rsyslog.d/keys/nixstats.ca
\$ActionSendStreamDriver gtls # use gtls netstream driver
\$ActionSendStreamDriverMode 1 # require TLS
\$ActionSendStreamDriverAuthMode x509/name # authenticate by hostname
\$ActionSendStreamDriverPermittedPeer log.nixstats.com

\$template NixFormat,\"<%pri%>%protocol-version% %timestamp:::date-rfc3339% %HOSTNAME% %app-name% %procid% %msgid% [`tail -c 25 /etc/nixstats-token.ini`@$logtoken tag=\\\"rsyslog\\\"] %msg%\\n\"

# Setup disk assisted queues
\$WorkDirectory /var/spool/rsyslog # where to place spool files
\$ActionQueueFileName fwdRule1     # unique name prefix for spool files
\$ActionQueueMaxDiskSpace 1g       # 1gb queue space
\$ActionQueueSaveOnShutdown on     # save messages to disk on shutdown
\$ActionQueueType LinkedList       # run asynchronously
\$ActionResumeRetryCount -1        # infinite retries if host is down

*.* @@log.nixstats.com:10514;NixFormat

#################END CONFIG FILE#########################
                "
                cat <<< "$configfile" > /etc/rsyslog.d/31-nixstats.conf
                command service rsyslog restart
fi


         fi
      fi;;

   CentOS*)
      if [ $# -lt 1 ]; then
         echo "NIXStats userid missing from runner command."
         __Norce
         exit 1
      else
      echo "Found CentOS..."

        id nixstats &>/dev/null
        if [[ $? -ne 0 ]]; then
                useradd --system --user-group --key USERGROUPS_ENAB=yes -M nixstats --shell /bin/false
                chown nixstats /var/log/nixstatsagent.log
        fi

        if [ "$(Linux_Version)" -le 6 ] ; then
            echo "Got CentOS 6"
           if [ "$(ensure_PIP)" != 'installed' ]; then
              echo "Installing ..."
              echo "Installing python2-setuptools ..."
              install "$(get_installer)" python-devel
              install "$(get_installer)" python-setuptools
              install "$(get_installer)" gcc
              install "$(get_installer)" which
              if [ "$(cat /etc/*-release | grep -io Oracle| head -1)" != 'Oracle' ]; then
                install "$(get_installer)" libevent-devel
              fi
              echo "Installing nixstatsagent ... "
              command easy_install nixstatsagent
              command easy_install netifaces
              command easy_install psutil

              echo "Generation a server id ..."
              if [ ! -f /etc/nixstats-token.ini ]; then
                  command nixstatshello "$1" /etc/nixstats-token.ini
              fi
              systemd "$1"

           elif [ "$(ensure_nixstatsagent)" != 'installed' ]; then
              echo "Installing nixstatsagent ... "
              install "$(get_installer)" python-devel
              install "$(get_installer)" python-setuptools
              install "$(get_installer)" gcc
              install "$(get_installer)" which
              if [ "$(cat /etc/*-release | grep -io Oracle| head -1)" != 'Oracle' ]; then
                install "$(get_installer)" libevent-devel
              fi
              echo "Installing nixstatsagent ... "
              command easy_install nixstatsagent
              command easy_install netifaces
              command easy_install psutil

              echo "Generation a server id ..."
              if [ ! -f /etc/nixstats-token.ini ]; then
                  command nixstatshello "$1" /etc/nixstats-token.ini
              fi
              systemd "$1"
           else
              echo "Upgrading nixstatsagent"
              install "$(get_installer)" python-devel
              install "$(get_installer)" python-setuptools
              install "$(get_installer)" gcc
              install "$(get_installer)" which
              if [ "$(cat /etc/*-release | grep -io Oracle| head -1)" != 'Oracle' ]; then
                install "$(get_installer)" libevent-devel
              fi
              install "$(get_installer)" which
              command easy_install -U nixstatsagent >> $logfile 2>&1
              if [ ! -f /etc/nixstats-token.ini ]; then
                  command nixstatshello "$1" /etc/nixstats-token.ini
              fi
              systemd "$1"
           fi
        elif [ "$(Linux_Version)" -gt 7 ]; then
            echo "Got CentOS 8..."
            if [ "$(ensure_PIP)" != 'installed' ]; then
               echo "Installing nixstatsagent for CentOS 8..."
               echo "Installing python3-pip ..."
               install "$(get_installer)" gcc
               install "$(get_installer)" which
               install "$(get_installer)" python3-devel
               install "$(get_installer)" python3

               echo "Installing nixstatsagent ... "
               command pip3 install setuptools --upgrade
               command pip3 install nixstatsagent --upgrade

               echo "Generation a server id ..."
               if [ ! -f /etc/nixstats-token.ini ]; then
                   command nixstatshello "$1" /etc/nixstats-token.ini
               fi
               systemd "$1"
            elif [ "$(ensure_PIP)" == 'installed' ]; then
               echo "Installing nixstatsagent for CentOS 8..."
               echo "Installing python3-pip ..."
               install "$(get_installer)" gcc
               install "$(get_installer)" which
               install "$(get_installer)" python3-devel
               install "$(get_installer)" python3

               echo "Installing nixstatsagent ... "
               command pip3 install setuptools --upgrade
               command pip3 install nixstatsagent --upgrade

               echo "Generation a server id ..."
               if [ ! -f /etc/nixstats-token.ini ]; then
                   command nixstatshello "$1" /etc/nixstats-token.ini
               fi
               systemd "$1"
            elif [ "$(ensure_nixstatsagent)" != 'installed' ]; then
               echo "Installing nixstatsagent for CentOS 8..."
               echo "Installing python3-pip ..."
               install "$(get_installer)" gcc
               install "$(get_installer)" which
               install "$(get_installer)" python3-devel
               install "$(get_installer)" python3

               echo "Installing nixstatsagent ... "
               command pip3 install setuptools --upgrade
               command pip3 install nixstatsagent --upgrade

               echo "Generation a server id ..."
               if [ ! -f /etc/nixstats-token.ini ]; then
                   command nixstatshello "$1" /etc/nixstats-token.ini
               fi
               systemd "$1"
            else
               echo "Upgrading nixstatsagent"
               install "$(get_installer)" gcc
               install "$(get_installer)" which
               install "$(get_installer)" python3-devel
               install "$(get_installer)" python3
               command  pip3 install nixstatsagent --upgrade >> $logfile 2>&1
               command  pip3 install psutil --upgrade >> $logfile 2>&1
               if [ ! -f /etc/nixstats-token.ini ]; then
                   command nixstatshello "$1" /etc/nixstats-token.ini
               fi
               systemd "$1"

            fi
                 elif [ "$(Linux_Version)" -gt 6 ]; then
                    echo "Installing nixstatsagent for CentOS 7..."
                     if [ "$(ensure_PIP)" != 'installed' ]; then
                        echo "Installing python-pip ..."
                        #if ! rpm -qa | grep -qw epel; then
                        #    command rpm -ivh http://dl.fedoraproject.org/pub/epel/6/x86_64/epel-release-6-8.noarch.rpm >>$logfile 2>&1
                        #    rc=$?
                        #    if [ "$rc" != "0" ]; then
                        #        echo epel repo install returned error code $?. Please see $logfile for details.
                        #    fi
                        #fi
                        #install "$(get_installer)" epel-release
                        install "$(get_installer)" python3-devel
                        install "$(get_installer)" python3-pip
                        #install "$(get_installer)" python-wheel
                        install "$(get_installer)" python3-setuptools
                        if [ "$(cat /etc/*-release | grep -io Oracle| head -1)" != 'Oracle' ]; then
                          install "$(get_installer)" libevent-devel
                        fi
                        install "$(get_installer)" gcc
                        install "$(get_installer)" which
                        #wget https://bootstrap.pypa.io/get-pip.py -O get-pip.py
                        #python get-pip.py

                        echo "Installing nixstatsagent ... "
                        #command pip3 install pip --upgrade
                        command pip3 install setuptools --upgrade
                        command pip3 install nixstatsagent --upgrade

                        echo "Generation a server id ..."
                        if [ ! -f /etc/nixstats-token.ini ]; then
                            command nixstatshello "$1" /etc/nixstats-token.ini
                        fi
                        systemd "$1"
                     elif [ "$(ensure_PIP)" == 'installed' ]; then
                        echo "Installing nixstatsagent"
                        #install "$(get_installer)" epel-release
                        install "$(get_installer)" python3-devel
                        install "$(get_installer)" python3-pip
                        #install "$(get_installer)" python-wheel
                        install "$(get_installer)" python3-setuptools
                        install "$(get_installer)" which
                        install "$(get_installer)" gcc
                        if [ "$(cat /etc/*-release | grep -io Oracle| head -1)" != 'Oracle' ]; then
                          install "$(get_installer)" libevent-devel
                        fi
                        #command wget https://bootstrap.pypa.io/get-pip.py -O get-pip.py
                        #command python get-pip.py
                        #command easy_install pip
                        #command pip3 install pip --upgrade
                        command pip3 install setuptools --upgrade
                        command pip3 install nixstatsagent --upgrade

                        #command easy_install pip
                        #command pip install nixstatsagent --upgrade
                        #command easy_install netifaces
                        #command easy_install psutil

                        echo "Generation a server id ..."
                        if [ ! -f /etc/nixstats-token.ini ]; then
                            command nixstatshello "$1" /etc/nixstats-token.ini
                        fi
                        systemd "$1"
                     elif [ "$(ensure_nixstatsagent)" != 'installed' ]; then
                        echo "Installing nixstatsagent ... "

                        install "$(get_installer)" which

                        install "$(get_installer)" python3-pip

                        install "$(get_installer)" python3-devel
                        install "$(get_installer)" python3-setuptools
                        install "$(get_installer)" gcc
                        if [ "$(cat /etc/*-release | grep -io Oracle| head -1)" != 'Oracle' ]; then
                          install "$(get_installer)" libevent-devel
                        fi
                        #command easy_install netifaces
                        #command easy_install psutil

                        command pip3 install pip --upgrade
                        command pip3 install setuptools --upgrade
                        command pip3 install nixstatsagent --upgrade

                        echo "Generation a server id ..."
                        if [ ! -f /etc/nixstats-token.ini ]; then
                            command nixstatshello "$1" /etc/nixstats-token.ini
                        fi
                        systemd "$1"
                     else
                        echo "Upgrading nixstatsagent"
                        #install "$(get_installer)" epel-release
#                        install "$(get_installer)" python-pip
#                        install "$(get_installer)" python-wheel
#                        install "$(get_installer)" python-devel
#                        install "$(get_installer)" python-setuptools
#                        install "$(get_installer)" gcc
                        if [ "$(cat /etc/*-release | grep -io Oracle| head -1)" != 'Oracle' ]; then
                          install "$(get_installer)" libevent-devel
                        fi
                        install "$(get_installer)" which
                        command  pip install nixstatsagent --upgrade >> $logfile 2>&1
                        command  pip install psutil --upgrade >> $logfile 2>&1
                        if [ ! -f /etc/nixstats-token.ini ]; then
                            command nixstatshello "$1" /etc/nixstats-token.ini
                        fi
                        systemd "$1"

                     fi
                  fi

        for i in "$@" ; do
          if [[ $i == "--cloudlinux" ]] ; then
             if [ -x "$(command -v nixstatsagent)" ]; then
                     sed -i 's/^fs.proc_super_gid/#commented out by nixstatsagent installer# fs.proc_super_gid/' /etc/sysctl.conf
                     echo "fs.proc_super_gid=$(id -g nixstats)" >> /etc/sysctl.conf
                     if ! grep -q nixstats "/etc/cagefs/exclude/systemuserlist"; then
                             echo "nixstats" >> /etc/cagefs/exclude/systemuserlist
                     fi
                     sysctl -p
                     cagefsctl --force-update
                     service lve_namespaces restart
                     service nixstatsagent restart
                 fi
              break
          fi
        done

         if [ "${#logtoken}" -eq 24 ]  && [ -f "/etc/nixstats-token.ini" ] && [ ! -f "/etc/rsyslog.d/31-nixstats.conf" ]; then
                        echo "Enabling logging..."
                        install "$(get_installer)" rsyslog-gnutls
                        if [ ! -d "/var/spool/rsyslog" ]; then
                             mkdir -p /var/spool/rsyslog
                        fi
                        if [ ! -d "/etc/rsyslog.d/keys" ]; then
                            mkdir -p /etc/rsyslog.d/keys
                       fi

                        wget -q -O /etc/rsyslog.d/keys/nixstats.ca https://nixstats.com/nixstats.ca
                        configfile="
         ##########################################################
         ### Rsyslog Template for Nixstats ###
         ##########################################################

         \$DefaultNetstreamDriverCAFile /etc/rsyslog.d/keys/nixstats.ca
         \$ActionSendStreamDriver gtls # use gtls netstream driver
         \$ActionSendStreamDriverMode 1 # require TLS
         \$ActionSendStreamDriverAuthMode x509/name # authenticate by hostname
         \$ActionSendStreamDriverPermittedPeer log.nixstats.com

         \$template NixFormat,\"<%pri%>%protocol-version% %timestamp:::date-rfc3339% %HOSTNAME% %app-name% %procid% %msgid% [`tail -c 25 /etc/nixstats-token.ini`@$logtoken tag=\\\"rsyslog\\\"] %msg%\\n\"

         # Setup disk assisted queues
         \$WorkDirectory /var/spool/rsyslog # where to place spool files
         \$ActionQueueFileName fwdRule1     # unique name prefix for spool files
         \$ActionQueueMaxDiskSpace 1g       # 1gb queue space
         \$ActionQueueSaveOnShutdown on     # save messages to disk on shutdown
         \$ActionQueueType LinkedList       # run asynchronously
         \$ActionResumeRetryCount -1        # infinite retries if host is down

         *.* @@log.nixstats.com:10514;NixFormat

         #################END CONFIG FILE#########################
                         "
                         cat <<< "$configfile" > /etc/rsyslog.d/31-nixstats.conf
                         command service rsyslog restart
         fi

      fi;;

   Amazon*)
      if [ $# -lt 1 ]; then
         echo "NIXStats userid missing from runner command."
         __Norce
         exit 1
      else
      echo "Found Amazon Linux..."

        id nixstats &>/dev/null
        if [[ $? -ne 0 ]]; then
                useradd --system --user-group --key USERGROUPS_ENAB=yes -M nixstats --shell /bin/false
                chown nixstats /var/log/nixstatsagent.log
        fi
        command easy_install pip
           install "$(get_installer)" python-devel
           install "$(get_installer)" python-setuptools
           install "$(get_installer)" python-pip
           install "$(get_installer)" gcc
        install "$(get_installer)" which gcc libevent-devel python27-devel python27-setuptools python27-pip -q --skip-broken
           if [ ! -f /usr/local/bin/pip2.7 ]; then
            pip install --upgrade pip
           fi

           if  [ -x "$(command -v pip)" ]; then
                command pip install --upgrade pip
                   command pip install nixstatsagent --upgrade
        else
                 command /usr/local/bin/pip install --upgrade pip
                command /usr/local/bin/pip install nixstatsagent --upgrade
           fi

        if [ ! -f /etc/nixstats-token.ini ]; then
           echo "Generation a server id ..."
           command nixstatshello "$1" /etc/nixstats-token.ini
        fi
        amazonlinux "$1"
       if [ "${#logtoken}" -eq 24 ] && [ -f "/etc/nixstats-token.ini" ] && [ ! -f "/etc/rsyslog.d/31-nixstats.conf" ]; then
           echo "Enabling logging..."
           install "$(get_installer)" rsyslog-gnutls
           if [ ! -d "/var/spool/rsyslog" ]; then
                mkdir -p /var/spool/rsyslog
           fi
           if [ ! -d "/etc/rsyslog.d/keys" ]; then
               mkdir -p /etc/rsyslog.d/keys
          fi

           wget -q -O /etc/rsyslog.d/keys/nixstats.ca https://nixstats.com/nixstats.ca
           configfile="
        ##########################################################
        ### Rsyslog Template for Nixstats ###
        ##########################################################

        \$DefaultNetstreamDriverCAFile /etc/rsyslog.d/keys/nixstats.ca
        \$ActionSendStreamDriver gtls # use gtls netstream driver
        \$ActionSendStreamDriverMode 1 # require TLS
        \$ActionSendStreamDriverAuthMode x509/name # authenticate by hostname
        \$ActionSendStreamDriverPermittedPeer log.nixstats.com

        \$template NixFormat,\"<%pri%>%protocol-version% %timestamp:::date-rfc3339% %HOSTNAME% %app-name% %procid% %msgid% [`tail -c 25 /etc/nixstats-token.ini`@$logtoken tag=\\\"rsyslog\\\"] %msg%\\n\"

        # Setup disk assisted queues
        \$WorkDirectory /var/spool/rsyslog # where to place spool files
        \$ActionQueueFileName fwdRule1     # unique name prefix for spool files
        \$ActionQueueMaxDiskSpace 1g       # 1gb queue space
        \$ActionQueueSaveOnShutdown on     # save messages to disk on shutdown
        \$ActionQueueType LinkedList       # run asynchronously
        \$ActionResumeRetryCount -1        # infinite retries if host is down

        *.* @@log.nixstats.com:10514;NixFormat

        #################END CONFIG FILE#########################
                        "
                        cat <<< "$configfile" > /etc/rsyslog.d/31-nixstats.conf
                        command service rsyslog restart
        fi

      fi;;

   Fedora*)
      if [ $# -lt 1 ]; then
         echo "NIXStats userid missing from runner command."
         __Norce
         exit 1
      else

        id nixstats &>/dev/null
        if [[ $? -ne 0 ]]; then
                useradd --system --user-group --key USERGROUPS_ENAB=yes -M nixstats --shell /bin/false
                chown nixstats /var/log/nixstatsagent.log
        fi

        if [ "$(Linux_Version)" -ge 24 ]; then
            if [ "$(ensure_PIP)" != 'installed' ]; then
               echo "Installing ..."
               echo "Installing python2-pip ..."

               install "$(get_installer)" python-devel
               install "$(get_installer)" cairo-devel
               install "$(get_installer)" libffi-devel
               install "$(get_installer)" gcc
               install "$(get_installer)" gcc-c++
               install "$(get_installer)" kernel-devel
               install "$(get_installer)" libxslt-devel
               install "$(get_installer)" libffi-devel
               install "$(get_installer)" openssl-devel
               install "$(get_installer)" redhat-rpm-config
               install "$(get_installer)" python-pip

               echo "Installing nixstatsagent ... "
               command  pip install --upgrade pip >> $logfile 2>&1
               command  pip install --upgrade nixstatsagent >> $logfile 2>&1
               rc=$?
                if [ "$rc" != "0" ]; then
                   echo pip install/upgrade returned error $?. Please see $logfile for details.
                   exit
               fi
               command  pip install nixstatsagent --upgrade >> $logfile 2>&1
               rc=$?
                if [ "$rc" != "0" ]; then
                   echo pip install of nixstatsagent returned error $?. Please see $logfile for details.
                   exit
               fi


               echo "Generation a server id ..."
               if [ ! -f /etc/nixstats-token.ini ]; then
                   command nixstatshello "$1" /etc/nixstats-token.ini
               fi
               systemd "$1"

            elif [ "$(ensure_nixstatsagent)" != 'installed' ]; then
               echo "Installing nixstatsagent ... "
               install "$(get_installer)" cairo-devel
               install "$(get_installer)" libffi-devel
               install "$(get_installer)" gcc
               install "$(get_installer)" gcc-c++
               install "$(get_installer)" kernel-devel
               install "$(get_installer)" python-devel
               install "$(get_installer)" libxslt-devel
               install "$(get_installer)" libffi-devel
               install "$(get_installer)" openssl-devel
               install "$(get_installer)" redhat-rpm-config
               install "$(get_installer)" python-pip
               command pip install --upgrade pip urllib3 >> $logfile 2>&1
               rc=$?
               if [ "$rc" != "0" ]; then
                      command  pip-2.7 install pip urllib3 --upgrade >>$logfile 2>&1
                      rcb=$?
               fi

               command pip install nixstatsagent --upgrade >> $logfile 2>&1
               rc=$?
               if [ "$rc" != "0" ]; then
                     command  pip-2.7 install nixstatsagent --upgrade >>$logfile 2>&1
                     rcb=$?
               fi


               if ! test -x /usr/local/bin/nixstatsagent && test -x /usr/bin/nixstatsagent; then
                    ln -s /usr/bin/nixstatsagent /usr/local/bin
               fi
               rc=$?
                if [ "$rc" != "0" ]; then
                   echo pip install of nixstatsagent returned error $?. Please see $logfile for details.
                   exit
               fi

               echo "Generation a server id ..."
               if [ ! -f /etc/nixstats-token.ini ]; then
                   command nixstatshello "$1" /etc/nixstats-token.ini
               fi
               systemd "$1"

            else

               echo "Upgrading nixstatsagent"
               install "$(get_installer)" python-devel
               install "$(get_installer)" cairo-devel
               install "$(get_installer)" libffi-devel
               install "$(get_installer)" gcc
               install "$(get_installer)" gcc-c++
               install "$(get_installer)" kernel-devel
               install "$(get_installer)" libxslt-devel
               install "$(get_installer)" libffi-devel
               install "$(get_installer)" openssl-devel
               install "$(get_installer)" redhat-rpm-config
               install "$(get_installer)" python-pip
               command pip install nixstatsagent --upgrade >> $logfile 2>&1
               if [ ! -f /etc/nixstats-token.ini ]; then
                   command nixstatshello "$1" /etc/nixstats-token.ini
               fi
               systemd "$1"

            fi
            if [ "${#logtoken}" -eq 24 ] && [ -f "/etc/nixstats-token.ini" ]  && [ ! -f "/etc/rsyslog.d/31-nixstats.conf" ]; then
                           echo "Enabling logging..."
                           install "$(get_installer)" rsyslog-gnutls
                           if [ ! -d "/var/spool/rsyslog" ]; then
                                mkdir -p /var/spool/rsyslog
                           fi
                           if [ ! -d "/etc/rsyslog.d/keys" ]; then
                               mkdir -p /etc/rsyslog.d/keys
                          fi

                           wget -q -O /etc/rsyslog.d/keys/nixstats.ca https://nixstats.com/nixstats.ca
                           configfile="
            ##########################################################
            ### Rsyslog Template for Nixstats ###
            ##########################################################

            \$DefaultNetstreamDriverCAFile /etc/rsyslog.d/keys/nixstats.ca
            \$ActionSendStreamDriver gtls # use gtls netstream driver
            \$ActionSendStreamDriverMode 1 # require TLS
            \$ActionSendStreamDriverAuthMode x509/name # authenticate by hostname
            \$ActionSendStreamDriverPermittedPeer log.nixstats.com

            \$template NixFormat,\"<%pri%>%protocol-version% %timestamp:::date-rfc3339% %HOSTNAME% %app-name% %procid% %msgid% [`tail -c 25 /etc/nixstats-token.ini`@$logtoken tag=\\\"rsyslog\\\"] %msg%\\n\"

            # Setup disk assisted queues
            \$WorkDirectory /var/spool/rsyslog # where to place spool files
            \$ActionQueueFileName fwdRule1     # unique name prefix for spool files
            \$ActionQueueMaxDiskSpace 1g       # 1gb queue space
            \$ActionQueueSaveOnShutdown on     # save messages to disk on shutdown
            \$ActionQueueType LinkedList       # run asynchronously
            \$ActionResumeRetryCount -1        # infinite retries if host is down

            *.* @@log.nixstats.com:10514;NixFormat

            #################END CONFIG FILE#########################
                            "
                            cat <<< "$configfile" > /etc/rsyslog.d/31-nixstats.conf
                            command service rsyslog restart
            fi
         fi
      fi;;

   FreeBSD*)
      if [ $# -lt 1 ]; then
         echo "NIXStats userid missing from runner command."
         __Norce
         exit 1
      else

         id nixstats &>/dev/null
         if [[ $? -ne 0 ]]; then
             pw adduser nixstats -c "User for nixstatsagent" -s /usr/local/bin/bash
         fi
         if [ "$(ensure_PIP)" != 'installed' ]; then
            echo "Installing ..."
            echo "Installing python2-pip ..."
            install "$(get_installer)" py27-pip
            install "$(get_installer)" bash

            echo "Installing nixstatsagent ... "
               command pip install --upgrade pip urllib3 >> $logfile 2>&1
               rc=$?
                if [ "$rc" != "0" ]; then
                   echo pip install/upgrade returned error $?. Please see $logfile for details.
                   exit
               fi
               command pip install nixstatsagent --upgrade >> $logfile 2>&1
               rc=$?
                if [ "$rc" != "0" ]; then
                   echo pip install of nixstatsagent returned error $?. Please see $logfile for details.
                   exit
               fi

            echo "Generation a server id ..."
            if [ ! -f /etc/nixstats-token.ini ]; then
                command nixstatshello "$1" /etc/nixstats-token.ini
            fi
            systemd "$1"

         elif [ "$(ensure_nixstatsagent)" != 'installed' ]; then
            echo "Installing nixstatsagent ... "
               command pip install --upgrade pip urllib3 >> $logfile 2>&1
               rc=$?
                if [ "$rc" != "0" ]; then
                   echo pip install/upgrade returned error $?. Please see $logfile for details.
                   exit
               fi
               command pip install nixstatsagent --upgrade >> $logfile 2>&1
               rc=$?
                if [ "$rc" != "0" ]; then
                   echo pip install of nixstatsagent returned error $?. Please see $logfile for details.
                   exit
               fi

            echo "Generation a server id ..."
            if [ ! -f /etc/nixstats-token.ini ]; then
                command nixstatshello "$1" /etc/nixstats-token.ini
            fi
            systemd "$1"

         else

            echo "Generation a server id ..."
            if [ ! -f /etc/nixstats-token.ini ]; then
                command nixstatshello "$1" /etc/nixstats-token.ini
            fi
            systemd "$1"

         fi


      fi;;
   *)

      __Requirements
esac

chmod u+s $(which ping)

