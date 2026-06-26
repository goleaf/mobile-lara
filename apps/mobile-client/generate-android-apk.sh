#!/usr/bin/env bash

set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$ROOT_DIR"

APK_PATH="$ROOT_DIR/nativephp/android/app/build/outputs/apk/release/app-release-unsigned.apk"

dotenv_value() {
    local key="$1"

    if [[ ! -f "$ROOT_DIR/.env" ]]; then
        return 0
    fi

    awk -F= -v key="$key" '$1 == key { print substr($0, length(key) + 2) }' "$ROOT_DIR/.env" \
        | tail -n 1 \
        | sed -e 's/^"//' -e 's/"$//' -e "s/^'//" -e "s/'$//"
}

resolve_java_home() {
    if [[ -n "${JAVA_HOME:-}" && -x "$JAVA_HOME/bin/java" ]]; then
        return 0
    fi

    if [[ -x /usr/libexec/java_home ]]; then
        JAVA_HOME="$(/usr/libexec/java_home -v 17 2>/dev/null || true)"
        export JAVA_HOME
    fi

    if [[ -z "${JAVA_HOME:-}" || ! -x "$JAVA_HOME/bin/java" ]]; then
        echo "Java 17 was not found. Install JDK 17 and set JAVA_HOME." >&2
        exit 1
    fi
}

resolve_android_sdk() {
    local env_sdk
    env_sdk="$(dotenv_value NATIVEPHP_ANDROID_SDK_LOCATION)"

    local candidates=(
        "${NATIVEPHP_ANDROID_SDK_LOCATION:-}"
        "${ANDROID_HOME:-}"
        "${ANDROID_SDK_ROOT:-}"
        "$env_sdk"
        "$HOME/Library/Android/sdk"
        "$HOME/Android/Sdk"
    )

    for sdk_path in "${candidates[@]}"; do
        if [[ -n "$sdk_path" && -d "$sdk_path/platform-tools" ]]; then
            export NATIVEPHP_ANDROID_SDK_LOCATION="$sdk_path"
            export ANDROID_HOME="$sdk_path"
            export ANDROID_SDK_ROOT="$sdk_path"
            return 0
        fi
    done

    echo "Android SDK was not found. Set NATIVEPHP_ANDROID_SDK_LOCATION or ANDROID_HOME." >&2
    exit 1
}

print_step() {
    printf '\n==> %s\n' "$1"
}

resolve_java_home
resolve_android_sdk

export PATH="$JAVA_HOME/bin:$ANDROID_HOME/emulator:$ANDROID_HOME/cmdline-tools/latest/bin:$ANDROID_HOME/platform-tools:$PATH"

print_step "Using Java: $JAVA_HOME"
print_step "Using Android SDK: $ANDROID_HOME"

if [[ ! -d "$ROOT_DIR/nativephp/android" ]]; then
    print_step "Installing NativePHP Android resources"
    php artisan native:install android --no-interaction --no-ansi
fi

print_step "Building frontend assets"
npm run build

print_step "Generating Android release APK"
php artisan native:run android --build=release --no-tty --no-interaction --no-ansi

if [[ ! -f "$APK_PATH" ]]; then
    echo "Expected APK was not generated: $APK_PATH" >&2
    exit 1
fi

print_step "APK generated"
ls -lh "$APK_PATH"

if command -v shasum >/dev/null 2>&1; then
    shasum -a 256 "$APK_PATH"
elif command -v sha256sum >/dev/null 2>&1; then
    sha256sum "$APK_PATH"
fi

printf '\nAPK path: %s\n' "$APK_PATH"
