<table>
@foreach($fields as $name => $value)
    <tr>
        <td style="padding: 10px; border: #e9e9e9 1px solid;"><p>{{ $name }}</p></td>
        <td style="padding: 10px; border: #e9e9e9 1px solid;background-color: #f8f8f8;"><p>{{ $value }}</p></td>
    </tr>
@endforeach
</table>
