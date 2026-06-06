#!/bin/bash

# Load environment variables
if [ -f .env ]; then
  export $(grep -v '^#' .env | xargs)
fi

CUSTOM_THEME_NAME=${CUSTOM_THEME_NAME:-front_theme}
THEME_FRAMEWORK=${2:-$THEME_FRAMEWORK}
THEME_FRAMEWORK=${THEME_FRAMEWORK:-tailwind}

BASE_THEME="front_theme"
BASE_THEME_PATH="web/themes/custom/$BASE_THEME"
THEME_PATH="web/themes/custom/$CUSTOM_THEME_NAME"
SCAFFOLD_PATH="web/modules/custom/base_theme/scaffold"
COMMON_PATH="$SCAFFOLD_PATH/common"
FRAMEWORK_PATH="$SCAFFOLD_PATH/$THEME_FRAMEWORK"

case "$1" in
  setup)
    echo "Setting up theme: $CUSTOM_THEME_NAME (framework: $THEME_FRAMEWORK)"

    # If theme already exists, skip setup
    if [ -d "$THEME_PATH" ]; then
      echo "Theme $CUSTOM_THEME_NAME already exists. Skipping scaffold."
    else
      # Create theme directory
      mkdir -p "$THEME_PATH"

      # Copy common files (configs, templates)
      echo "Copying common files..."
      cp -r "$COMMON_PATH"/* "$THEME_PATH/"

      # Copy framework-specific files
      if [ -d "$FRAMEWORK_PATH" ]; then
        echo "Copying $THEME_FRAMEWORK specific files..."
        cp -r "$FRAMEWORK_PATH"/* "$THEME_PATH/"
      else
        echo "Warning: Framework path $FRAMEWORK_PATH not found, using tailwind as fallback"
        cp -r "$SCAFFOLD_PATH/tailwind"/* "$THEME_PATH/"
      fi

      # Create dist directories
      mkdir -p "$THEME_PATH/dist/css"
      mkdir -p "$THEME_PATH/dist/js"
    fi

    # Rename theme files to match CUSTOM_THEME_NAME
    echo "Renaming files to match theme name..."
    find "$THEME_PATH" -maxdepth 1 -name "*$BASE_THEME*" -type f | while read file; do
      newfile="${file//$BASE_THEME/$CUSTOM_THEME_NAME}"
      mv "$file" "$newfile"
    done

    # Replace content inside files
    echo "Updating references in files..."
    find "$THEME_PATH" -type f \( -name "*.yml" -o -name "*.yaml" -o -name "*.json" -o -name "*.js" -o -name "*.theme" -o -name "*.scss" \) -exec sed -i "s/$BASE_THEME/$CUSTOM_THEME_NAME/g" {} \;

    # Set as default theme in Drupal if drush is available
    if command -v drush &> /dev/null; then
      drush theme:install $CUSTOM_THEME_NAME -y
      drush config-set system.theme default $CUSTOM_THEME_NAME -y
    fi
    ;;
esac
