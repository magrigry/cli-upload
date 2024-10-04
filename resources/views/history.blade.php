<h2>
    History
</h2>

<p>
    Your uploaded files from browser for the current session will be show here.
</p>

<table>
    <tr>
        <th>File</th>
        <th>Size</th>
        <th>Date</th>
        <th colspan="2">Actions</th>
    </tr>
    <tbody>
        @foreach($uploads as $upload)
            <tr>
                <td>
                    <a style="word-wrap: anywhere" href="{{ route('api.download', ['upload' => $upload]) }}">
                        {{ $upload->filename }}
                    </a>
                </td>
                <td>
                    ({{ ByteUnits\Metric::bytes($upload->size)->format() }})
                </td>
                <td>
                    {{ $upload->created_at->longRelativeDiffForHumans() }}

                    @if ($upload->getEstimatedExpireFromNow())
                        <br> (Expire in {{ $upload->getEstimatedExpireFromNow()->forHumans() }})
                    @endif
                </td>
                <td>
                    <form method="POST" action="{{ route('session.delete', ['upload' => $upload]) }}">
                        @csrf
                        @method('DELETE')
                        <a href="#" onclick="this.closest('form').submit(); return false;">Delete</a>
                    </form>
                </td>
                <td>
                    <a href="#" onclick="document.getElementById('{{ 'code-modal-' . $upload->id }}').showModal();return false;">Download code example</a>
                </td>
            </tr>
            <dialog id="{{ 'code-modal-' . $upload->id }}">
                <article>
                    <header>
                        <button aria-label="Close" rel="prev" onclick="document.getElementById('{{ 'code-modal-' . $upload->id }}').close()"></button>
                    </header>
                    <p>
                        <x-code language="bash">
                            curl "{{ route('api.download', ['upload' => $upload]) }}" > "{{ $upload->filename }}"
                        </x-code>
                    </p>
                </article>
            </dialog>
        @endforeach
    </tbody>

</table>

{{ $uploads->links() }}
