<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>CLI-Upload</title>

    <link rel="stylesheet" href="{{ asset('/css/pico/pico.classless.min.css') }}">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/highlightjs/cdn-release@11.9.0/build/styles/github-dark.min.css">
    <link rel="stylesheet" href="https://unpkg.com/highlightjs-copy/dist/highlightjs-copy.min.css"/>

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

    </style>

    <script src="https://cdn.jsdelivr.net/gh/highlightjs/cdn-release@11.9.0/build/highlight.min.js"></script>
    <script src="https://unpkg.com/highlightjs-copy/dist/highlightjs-copy.min.js"></script>
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
                <li><a href="#" class="contrast">Github</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <h1></h1>
        <p>
            Upload files from command line to easily share between servers.
            Inspired from <a href="https://bashupload.com">bashupload</a>.
        </p>

        <p>Limitations</p>
        <ul>
            <li>Expire after 1 hour</li>
            <li>Max file size : {{ $maxSize }}</li>
            <li>Global maximum capacity : {{ $maxCapacity }}  </li>
            <li>Global Current capacity used : {{ $usedCapacity }}</li>
            <li>Per IP maximum capacity : {{ $maxCapacityPerIP }}  </li>
            <li>Per IP ({{ request()->ip() }}) Capacity used : {{ $usedCapacityPerIP }}</li>
        </ul>

        <hr />

        <article>
            <h2>From Browser</h2>
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

        <p>
           Replace the filename with your filename at the end of the command.
        </p>

        <details>
            <summary><small>Click to see details and non minified version</small></summary>
            <p>
                A oneline bash command that use OpenSSL to encrypt your file and send it to {{ $host }}.
                It generate a random 256 bits key (which is not send to the server) using <code>$(openssl rand -hex 32)</code>,
                encrypt your file with the key using <code>OpenSSL</code> and  <code>aes-256-cbc</code>, then send it to the server.
                The server answer with the download URL and the bash function generate the command to download the file and decrypt it.
                Using
            </p>
            <x-code language="bash">
                @include('commands.curl-openssl-bash') && cliupload your_file.txt
            </x-code>
        </details>

        <x-code language="bash" :minify="true">
            @include('commands.curl-openssl-bash') && cliupload your_file.txt
        </x-code>

        <p>Short syntax remote based :</p>
        <x-code language="bash">
            curl -fsSL {{ route('scripts', ['name' => 'curl-openssl-bash']) }} | bash && cliupload your_file.txt
        </x-code>

    </main>
</body>
</html>
