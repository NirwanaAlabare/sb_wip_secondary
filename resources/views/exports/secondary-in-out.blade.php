<table>
    <thead>
        <tr>
            <th style="border: 1px solid black; font-weight: 800;">Time IN</th>
            <th style="border: 1px solid black; font-weight: 800;">Time OUT</th>
            <th style="border: 1px solid black; font-weight: 800;">Line</th>
            <th style="border: 1px solid black; font-weight: 800;">No. WS</th>
            <th style="border: 1px solid black; font-weight: 800;">Style</th>
            <th style="border: 1px solid black; font-weight: 800;">Color</th>
            <th style="border: 1px solid black; font-weight: 800;">Size</th>
            <th style="border: 1px solid black; font-weight: 800;">Status</th>
            <th style="border: 1px solid black; font-weight: 800;">Type</th>
            <th style="border: 1px solid black; font-weight: 800;">Area</th>
            <th style="border: 1px solid black; font-weight: 800;">IN By</th>
            <th style="border: 1px solid black; font-weight: 800;">OUT By</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($secondaryInOut as $secInOut)
            <tr>
                <td style="border: 1px solid black;">{{ $secInOut->time_in }}</td>
                <td style="border: 1px solid black;">{{ $secInOut->time_out }}</td>
                <td style="border: 1px solid black;">{{ $secInOut->sewing_line }}</td>
                <td style="border: 1px solid black;">{{ $secInOut->no_ws }}</td>
                <td style="border: 1px solid black;">{{ $secInOut->style }}</td>
                <td style="border: 1px solid black;">{{ $secInOut->color }}</td>
                <td style="border: 1px solid black;">{{ $secInOut->size }}</td>
                <td style="border: 1px solid black;">{{ $secInOut->status }}</td>
                <td style="border: 1px solid black;">{{ $secInOut->defect_type }}</td>
                <td style="border: 1px solid black;">{{ $secInOut->defect_area }}</td>
                <td style="border: 1px solid black;">{{ $secInOut->user_in }}</td>
                <td style="border: 1px solid black;">{{ $secInOut->user_out }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
