#!/bin/bash

# Determine the directory where the script is located
BASE_PATH="$( cd "$( dirname "${BASH_SOURCE[0]}" )" &> /dev/null && pwd )"

concatenate_js() {
    > "$BASE_PATH/scripts.js" # Clear the file before concatenation
    for file in "$BASE_PATH/js/"*.js; do
        echo -e "\n// $(basename "$file")" >> "$BASE_PATH/scripts.js"
        cat "$file" >> "$BASE_PATH/scripts.js"
    done
    echo "Concatenation complete."
}

# Watch function using modification times
watch_files() {
    local last_mod_time=$(date +%s)

    while true; do
        local current_mod_time=$(find "$BASE_PATH/js" -type f -exec stat -c "%Y" {} \; | sort -nr | head -n 1)
        if [[ "$current_mod_time" != "$last_mod_time" ]]; then
            last_mod_time=$current_mod_time
            concatenate_js
        fi
        sleep 2 # Check every 2 seconds
    done
}

# Check for --watch argument
if [ "$1" == "--watch" ]; then
    echo "Watching for changes in the js folder..."
    watch_files
else
    concatenate_js
fi