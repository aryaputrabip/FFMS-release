<table>
    <thead>
    <tr>
        <th>JOIN DATE</th>
        <th>INVOICE</th>
        <th>MEMBER NAME</th>
        <th>EMAIL</th>
        <th>TYPE MEMBERSHIP</th>
        <th>NO TELP</th>
        <th>FITNESS CONSULTANT</th>
        <th>CS</th>
        <th>REMARKS</th>
    </tr>
    </thead>
    <tbody>
    @foreach($invoices as $invoice)
        <tr>
            <td>{{ $invoice->member_id }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
