clidownload() {
    HMAC=$1
    KEY=$2 &&
    FILENAME=$3 &&
    URL=$4;
    [[ $URL != http* ]] && echo 'An error occurred : ' && echo $URL;
    [[ $URL == http* ]] &&
    echo '\nDownload your file with the following command' &&
    echo '---------' &&
    echo -n 'curl "'$URL'" | openssl enc -aes-256-cbc -d -salt -pbkdf2 -out "'$FILENAME'" -iter 1000000 -md sha512 -pass pass:"'$KEY'"' &&
    echo ' && COMP=$(echo -n "$FILENAME" | openssl dgst -sha256 -hmac "$KEY") && [ $COMP = "'$HMAC'" ] || (echo "Error: Failed to verify file integrity" && rm "'$FILENAME'") ' &&
    echo '---------'
} &&
cliupload() {
    FILEPATH=$1;
    [ -f "$FILEPATH" ] || echo "The file $FILEPATH does not exists" >&2;
    KEY=$(openssl rand -hex 32);
    HMAC=$(echo -n "$FILEPATH" | openssl dgst -sha256 -hmac "$KEY");
    FILENAME=$(basename $FILEPATH) &&
    [ -f "$FILEPATH" ] && clidownload $HMAC $KEY $FILENAME "$(openssl enc -aes-256-cbc -in "$FILEPATH" -salt -pbkdf2 -iter 1000000 -md sha512 -pass pass:"$KEY" | curl -H 'Accept: text/plain' -T - {{ urldecode(route('api.upload', ['filename' => '$FILENAME'])) }} )"
}
