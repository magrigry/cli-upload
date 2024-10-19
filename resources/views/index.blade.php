<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">

    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>CLI-Upload</title>

    <link rel="stylesheet" href="{{ asset('/css/pico/pico.classless.min.css') }}">

    <link rel="icon" type="image/x-icon" href="{{ asset('/favicon.ico') }}">

{{--    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/highlightjs/cdn-release@11.9.0/build/styles/github-dark.min.css" integrity="sha384-wH75j6z1lH97ZOpMOInqhgKzFkAInZPPSPlZpYKYTOqsaizPvhQZmAtLcPKXpLyH" crossorigin="anonymous">--}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/highlightjs/cdn-release@11.9.0/build/styles/an-old-hope.min.css" integrity="sha384-jECk4G9CH/HRhGkMUheeoDd94btueS+LxzL99cgP3S/dCa3Sc/2HOgm4jOzqa3Bo" crossorigin="anonymous">
    <link rel="stylesheet" href="https://unpkg.com/highlightjs-copy/dist/highlightjs-copy.min.css" integrity="sha384-jx4j2QNE8PcYHQikjfTfY6TM0sYVodTr0OGqUfAR6bKYJBgW91lTieqkghTu9+Kk" crossorigin="anonymous">

    <style>

        /*[data-copied='true'] {*/
        /*    padding: 5px;*/
        /*}*/

        /*[data-copied] {*/
        /*    margin-top: 10px;*/
        /*}*/

        [role='navigation'] > .hidden {
            display: none;
        }

        :root {
            --pico-font-family-sans-serif: Inter, system-ui, "Segoe UI", Roboto, Oxygen, Ubuntu, Cantarell, Helvetica, Arial, "Helvetica Neue", sans-serif, var(--pico-font-family-emoji);
            --pico-font-size: 87.5%;
            /* Original: 100% */
            --pico-line-height: 1.25;
            /* Original: 1.5 */
            --pico-form-element-spacing-vertical: 0.5rem;
            /* Original: 1rem */
            --pico-form-element-spacing-horizontal: 1.0rem;
            /* Original: 1.25rem */
            --pico-border-radius: 0.375rem;
            /* Original: 0.25rem */
        }

        @media (min-width: 576px) {
            :root {
                --pico-font-size: 87.5%;
                /* Original: 106.25% */
            }
        }

        @media (min-width: 768px) {
            :root {
                --pico-font-size: 87.5%;
                /* Original: 112.5% */
            }
        }

        @media (min-width: 1024px) {
            :root {
                --pico-font-size: 87.5%;
                /* Original: 118.75% */
            }
        }

        @media (min-width: 1280px) {
            :root {
                --pico-font-size: 87.5%;
                /* Original: 125% */
            }
        }

        @media (min-width: 1536px) {
            :root {
                --pico-font-size: 87.5%;
                /* Original: 131.25% */
            }
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            --pico-font-weight: 600;
            /* Original: 700 */
        }

        article {
            border: 1px solid var(--pico-muted-border-color);
            /* Original doesn't have a border */
            border-radius: calc(var(--pico-border-radius) * 2);
            /* Original: var(--pico-border-radius) */
        }

        article>footer {
            border-radius: calc(var(--pico-border-radius) * 2);
            /* Original: var(--pico-border-radius) */
        }

        hr {
            margin-top: 30px;
            margin-bottom: 30px;
        }

    </style>

    <script src="https://cdn.jsdelivr.net/gh/highlightjs/cdn-release@11.9.0/build/highlight.min.js" integrity="sha384-F/bZzf7p3Joyp5psL90p/p89AZJsndkSoGwRpXcZhleCWhd8SnRuoYo4d0yirjJp" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/highlightjs-copy/dist/highlightjs-copy.min.js" integrity="sha384-0/jh9+ifwJ5mqtDZ+DWdwgFjZ8I4HIfXqaWJi2mdeAwy8aUPlw5dTYNsqAqNE4yD" crossorigin="anonymous"></script>
    <script>
        hljs.highlightAll();
        hljs.addPlugin(new CopyButtonPlugin());
    </script>
</head>
<body>
    <header>
        <nav>
            <ul>
                <li><h1>CLI Upload</h1></li>
            </ul>
            <ul>
                <li><a href="https://github.com/magrigry/cli-upload" class="contrast">Github</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <h1></h1>
        <p>
            Upload files from command line to easily share between servers with 0 knowledge encryption.
            Inspired from <a href="https://bashupload.com">bashupload</a>.
        </p>

        <details>
            <summary>Click here to see limitations</summary>
            <ul>
                <li>Expire after 1 hour</li>
                <li>Max file size : {{ $maxSize }}</li>
                <li>Global maximum capacity : {{ $maxCapacity }}  </li>
                <li>Global Current capacity used : {{ $usedCapacity }}</li>
                <li>Per IP maximum capacity : {{ $maxCapacityPerIP }}  </li>
                <li>Per IP ({{ request()->ip() }}) capacity used : {{ $usedCapacityPerIP }}</li>
            </ul>
        </details>

        <hr />

        <p>
            Replace <code>your_file.txt</code> with your filename at the end of the command. More details and alternatives below
        </p>
        <x-code language="bash">
            bash <(curl -fsSL {{ route('scripts', ['name' => 'curl-openssl-bash']) }}) your_file.txt
        </x-code>

        <hr />

        <article>
            <h2>From Browser <small>(not encrypted)</small></h2>
            <form action="{{ route('session.upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <fieldset role="group">
                    <input type="file" required name="files[]" multiple="multiple">
                    <input type="submit">
                </fieldset>
            </form>

            @error('files.*')
                {{ $message }}
            @enderror

            @includeUnless($uploads->isEmpty(), 'history')

        </article>

        <hr />

        <h2>Curl with Bash, OpenSSL and aes-256-cbc</h2>

        <details>
            <summary>Click here to see details and non minified version</summary>
            <p>
                A oneline bash command that use OpenSSL to encrypt your file and send it to {{ $host }}.
                It generate a random 256 bits key (which is not send to the server) using <code>$(openssl rand -hex 32)</code>,
                encrypt your file with the key using <code>OpenSSL</code> and <code>aes-256-cbc</code>, then send it to the server.
                The server answer with the download URL and the bash function generate the command to download the file and decrypt it.
                Using
            </p>
            <x-code language="bash">
                @include('commands.curl-openssl-bash') && cliupload your_file.txt
            </x-code>
        </details>

        <p>Long syntax with local script :</p>
        <x-code language="bash" :minify="true">
            @include('commands.curl-openssl-bash') && cliupload your_file.txt
        </x-code>

        <p>Short syntax remote based :</p>
        <x-code language="bash">
            bash <(curl -fsSL {{ route('scripts', ['name' => 'curl-openssl-bash']) }}) your_file.txt
        </x-code>

        <h2>Bash and curl <small>with no encryption</small></h2>

        <x-code language="bash">
            echo "curl -O $(curl {{ urldecode(route('api.upload', ['filename' => ' '])) }} -T your_file.txt)"
        </x-code>

    </main>
</body>
</html>
