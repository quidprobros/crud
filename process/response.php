<!-- -*- mode:web -*- -->
<table class="table">
    <thead>
        <tr>
            <th>Subscriber</th>
            <th>Email</th>
            <th>Telephone</th>
            <th>Message</th>
        </tr>
    </thead>
    <?PHP
    if (Flight::has('sqlQueryResults')) {
        while ($row = Flight::get('sqlQueryResults')->fetch())
        {
            $cleanMessage = Flight::censorThis($row['subscriberMessage']);
    ?>
        <tr>
            <td><?=$row['subscriberName']?></td>
            <td><?=$row['subscriberEmail']?></td>
            <td><pre><?=$row['subscriberPhone']?></pre></td>
            <td><?=$cleanMessage?></td>
        </tr>
    <?PHP
    }
    }
    ?>
</table>
