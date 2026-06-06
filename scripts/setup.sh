#!/bin/bash
# Setup script for ArchTech Suite - initializes DDEV, installs base recipes, and configures services.
set -euo pipefail

echo "=== ArchTech Suite Setup ==="

# Check DDEV is installed
if ! command -v ddev &>/dev/null; then
    echo "ERROR: DDEV is not installed. Install it first: https://ddev.com/get-started/"
    exit 1
fi

# Copy AGENTS.md if not at root
if [ ! -f "../AGENTS.md" ]; then
    cp AGENTS.md ../AGENTS.md
    echo "AGENTS.md copied to project root."
fi

echo ""
echo "Setup complete. Next steps:"
echo "  1. Run 'ddev start' in the project root"
echo "  2. Run 'ddev composer install'"
echo "  3. Run 'ddev install' to install Drupal with base recipes"
echo "  4. Apply bounded context recipes as needed:"
for recipe in recipes/archtech_*/; do
    recipe_name=$(basename "$recipe")
    echo "     - ddev recipe apply ../recipes/$recipe_name"
done
