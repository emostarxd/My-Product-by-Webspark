#!/bin/bash
# Oliver's script to collect all php files with names, pathes and content and save in one file
# Helpful for future error checking / debug 

# Clear or create the output file
> allpluginfiles.txt

# Find all PHP files recursively in current directory
find . -type f -name "*.php" | while read -r file; do
    # Add file path
    echo "=== File: $file ===" >> allpluginfiles.txt
    # Add small separator
    echo "-----------------" >> allpluginfiles.txt
    # Add file contents
    cat "$file" >> allpluginfiles.txt
    # Add large separator
    echo -e "\n==========================================\n" >> allpluginfiles.txt
done

echo "Files have been collected to allpluginfiles.txt"
