clidownload() {
    KEY=$1 &&
    FILENAME=$2 &&
    URL=$3;
    [[ $URL != http* ]] && echo 'An error occurred : ' && echo $URL;
    [[ $URL == http* ]] &&
    echo '\nDownload your file with the following command' &&
    echo '---------' &&
    echo 'curl "'$URL'" | openssl enc -aes-256-cbc -d -salt -pbkdf2 -out "'$FILENAME'" -iter 1000000 -md sha512 -pass pass:"'$KEY'"' &&
    echo '---------'
} &&
cliupload() {
    FILEPATH=$1;
    [ -f "$FILEPATH" ] || echo "The file $FILEPATH does not exists" >&2;
    FILENAME=$(basename $FILEPATH) &&
    KEY=$(openssl rand -hex 32) &&
    [ -f "$FILEPATH" ] && clidownload $KEY $FILENAME "$(openssl enc -aes-256-cbc -in "$FILEPATH" -salt -pbkdf2 -iter 1000000 -md sha512 -pass pass:"$KEY" | curl -H 'Accept: text/plain' -T - {{ urldecode(route('api.upload', ['filename' => '$FILENAME'])) }} )"
}
