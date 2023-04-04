#!/usr/bin/env bash
set -e

cd "$(
  cd "$(dirname "$0")" || exit
  pwd
)" || exit

ME=$(basename $0)
PROXY=""
ASSETS_PATH="https://github.com/Loyalsoldier/clash-rules/releases/latest/download"

curl() {
  $(type -P curl) -L -q -fS -x "$PROXY" --retry 5 --retry-delay 10 --retry-max-time 60 "$@"
}

test_proxy(){
  echo "Test proxy.. $PROXY"
  curl -x "$PROXY" https://httpbin.org/ip
}

update_clash_rules() {
  test_proxy

  curl -O "${ASSETS_PATH}/apple.txt"
  curl -O "${ASSETS_PATH}/applications.txt"
  curl -O "${ASSETS_PATH}/cncidr.txt"
  curl -O "${ASSETS_PATH}/direct.txt"
  curl -O "${ASSETS_PATH}/gfw.txt"
  curl -O "${ASSETS_PATH}/google.txt"
  curl -O "${ASSETS_PATH}/greatfire.txt"
  curl -O "${ASSETS_PATH}/icloud.txt"
  curl -O "${ASSETS_PATH}/lancidr.txt"
  curl -O "${ASSETS_PATH}/private.txt"
  curl -O "${ASSETS_PATH}/proxy.txt"
  curl -O "${ASSETS_PATH}/reject.txt"
  curl -O "${ASSETS_PATH}/telegramcidr.txt"
  curl -O "${ASSETS_PATH}/tld-not-cn.txt"

  echo "Success.."
}

show_help() {
  echo "usage: $0 [-c | -f | -h | -l | -p]"
  echo '  -c, --check     Check if can be updated'
  echo '  -f, --force     Force installation of the latest version of clash-rules'
  echo '  -h, --help      Show help'
  echo '  -p, --proxy     Download through a proxy server, e.g., -p http://127.0.0.1:8080 or -p socks5://127.0.0.1:1080'
  exit 0
}

judgment_parameters() {
  while [[ "$#" -gt '0' ]]; do
    case "$1" in
      '-c' | '--check')
        CHECK='1'
        break
        ;;
      '-f' | '--force')
        FORCE='1'
        break
        ;;
      '-h' | '--help')
        HELP='1'
        break
        ;;
      '-p' | '--proxy')
        if [[ -z "${2:?error: Please specify the proxy server address.}" ]]; then
          exit 1
        fi
        PROXY="$2"
        shift
        ;;
      *)
        echo "$0: unknown option -- -"
        exit 1
        ;;
    esac
    shift
  done
}

main() {
  judgment_parameters "$@"

  # Parameter information
  [[ "$HELP" -eq '1' ]] && show_help
#  [[ "$CHECK" -eq '1' ]] && check_update

  update_clash_rules

}

main "$@"
