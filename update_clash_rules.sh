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
  if [[ -n $PROXY ]]; then
    echo "Testing proxy at $PROXY..."
    if curl -x "$PROXY" https://httpbin.org/ip; then
      echo "Proxy test successful."
    else
      echo "Proxy test failed."
      exit 1
    fi
  else
    echo "No proxy set."
  fi
}

update_clash_rules() {
  test_proxy

  # Define all the file names in an array
  files=(
    "apple.txt" "applications.txt" "cncidr.txt" "direct.txt" "gfw.txt"
    "google.txt" "greatfire.txt" "icloud.txt" "lancidr.txt" "private.txt"
    "proxy.txt" "reject.txt" "telegramcidr.txt" "tld-not-cn.txt"
  )

  local dir="clash-rules"

  # Loop through the array and download each file
  for file in "${files[@]}"; do
    echo "Downloading $file..."
    curl -o "${dir}/${file}" "${ASSETS_PATH}/${file}" || {
      echo "Failed to download $file"
      continue
    }
  done

  echo "All files downloaded successfully."
}

show_help() {
  cat <<EOF
Usage: $ME [OPTIONS]

OPTIONS:
  -c, --check     Check if updates are available
  -f, --force     Force update the rules
  -h, --help      Show this help message
  -p, --proxy     Set a proxy server, e.g., http://127.0.0.1:7890

EXAMPLES:
  $ME --proxy 127.0.0.1:7890
  $ME --force
EOF
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
        shift
        PROXY=${1:?Error: Please specify the proxy server address.}
        ;;
      *)
        echo "Error: Unknown option $1"
        show_help
        ;;
    esac
    shift
  done
}

main() {
  judgment_parameters "$@"

  # Parameter information
  [[ "$HELP" -eq '1' ]] && show_help

  update_clash_rules

}

main "$@"
