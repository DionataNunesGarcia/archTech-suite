#!/bin/bash
# Script to convert existing files to use tabs with 2 spaces width
# This script converts leading spaces to tabs based on .editorconfig settings

set -e

echo "Converting indentation to tabs (2 spaces width)..."

# Find all text files (excluding vendor, node_modules, .git, and binary files)
files=$(find . -type f \( \
  -name "*.php" -o \
  -name "*.js" -o \
  -name "*.css" -o \
  -name "*.twig" -o \
  -name "*.yml" -o \
  -name "*.yaml" -o \
  -name "*.json" -o \
  -name "*.md" -o \
  -name "*.txt" -o \
  -name "*.html" -o \
  -name "*.module" -o \
  -name "*.theme" -o \
  -name "*.inc" -o \
  -name "*.install" -o \
  -name "*.profile" -o \
  -name "*.sh" \
  \) -not -path "./vendor/*" -not -path "./node_modules/*" -not -path "./.git/*")

for file in $files; do
  if [ -f "$file" ]; then
    # Convert 2 spaces to 1 tab, 4 spaces to 2 tabs, etc.
    sed -i 's/  /\t/g' "$file"
    echo "Converted: $file"
  fi
done

echo "Done!"
