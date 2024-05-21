<html>
<head>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
<div class="mx-10 mt-5 mb-4 flex flex-row justify-between items-center">
    <div class="ml-3 w-32 flex flex-row justify-center cursor-pointer" onclick="clickHome()">
        <img class="w-16 mx-4" src="{{asset('images/logo.png')}}">
        <img class="w-32 self-center" src="{{asset('images/KADAMBA.png')}}">
    </div>
    <div class=" flex flex-row justify-center items-center">
    <form class="flex flex-row items-center my-auto" id="portalForm">
        <a href="/transactions" class="bg-secondary px-6 py-3 mx-4 rounded-lg text-primary p-0 font-body"> TRANSACTIONS</a>
        @if(\Illuminate\Support\Facades\Auth::user()->pan != 'XXXXXXXXXX')
            <button type="submit" class="bg-secondary px-6 py-3 mx-4 self-center rounded-lg text-primary font-body"> PORTAL</button>
        @endif
    </form>
    <form method="POST" action="/logout" class="flex flex-row items-center my-auto">
        @csrf
        <button type="submit" class="bg-primary px-6 py-3 mx-4 rounded-lg text-secondary font-body"> LOGOUT</button>
    </form>
    </div>
</div>
<div class="flex flex-row justify-center items-center">
    <p class="text-primary p-0 mr-6 font-body self-center">Total Agent Commission : <span class="font-bold"> {{$agentTotal}}</span></p>
    <input class="border border-primary bg-white rounded-lg text-primary text-center p-2" type="text" name="datetimes" />
    @if(\Illuminate\Support\Facades\Auth::user()->pan == 'XXXXXXXXXX')
        <select class="bg-secondary px-6 py-3 mx-4 self-center rounded-lg text-primary font-body" onchange="userSelected(this)">
            <option value=" " selected >Please Select</option>
            @foreach($users as $user)
                <option value="{{$user->pan}}" >{{$user->name}}</option>
            @endforeach
        </select>
            <p class="text-primary p-0 mr-6 font-body self-center">Total Kadamba Commission : <span class="font-bold"> {{$kadambaTotal}}</span></p>
    @endif
</div>

<table class="w-full mt-8 ">
    <thead>
        <tr>
            <th class="text-center text-primary font-bold py-3">ID</th>
            <th class="text-center text-primary font-bold">Type</th>
            <th class="text-center text-primary font-bold">Phone</th>
            <th class="text-center text-primary font-bold">Amount</th>
            @if(\Illuminate\Support\Facades\Auth::user()->pan == 'XXXXXXXXXX')
                <th class="text-center text-primary font-bold">User</th>
                <th class="text-center text-primary font-bold">Kadamba TDS</th>
                <th class="text-center text-primary font-bold">Kadamba Comm</th>
            @endif
            <th class="text-center text-primary font-bold">Agent Comm</th>
            <th class="text-center text-primary font-bold">Agent TDS</th>
            <th class="text-center text-primary font-bold">Date</th>

        </tr>
    </thead>
    <tbody class="text-center">
    @foreach($transactions as $transaction)
        <tr class=" my-1">
            <td class="text-primary py-3">{{$transaction->id}}</td>
            <td class=" text-primary">{{$transaction->type}}</td>
            <td class=" text-primary">{{$transaction->customer_params}}</td>
            <td class=" text-primary">{{$transaction->amount}}</td>
            @if(\Illuminate\Support\Facades\Auth::user()->pan == 'XXXXXXXXXX')
                <td class=" text-primary">{{$transaction->user['name']}}</td>
                <td class=" text-primary">{{$transaction->total_tds}}</td>
                <td class=" text-primary">{{$transaction->kadamba_comm}}</td>
            @endif
            <td class=" text-primary">{{$transaction->agent_comm}}</td>
            <td class=" text-primary">{{$transaction->agent_tds}}</td>
            <td class=" text-primary">{{$transaction->created_at->toDateString()}}</td>
        </tr>
    @endforeach
    </tbody>
</table>
{{ $transactions->withQueryString()->links() }}



</body>
<script>



    const urlParams = new URLSearchParams(window.location.search);
    let from = new Date();
    let to = new Date();

    if(urlParams.has('from')){
        from = new Date(urlParams.get('from'))
    }
    if (urlParams.has('to')){
        to = new Date(urlParams.get('to'));
    }

    function userSelected(select){
        if(select.value === 'XXXXXXXXXX'){
            window.location.href = '/transactions'
        }else{
            if(urlParams.has('from') && urlParams.has('to') ){
                window.location.href = '/transactions?from='+urlParams.get('from')+'&to='+urlParams.get('to')+'&pan='+select.value;
            }else{
                window.location.href = '/transactions?pan='+select.value;
            }
        }
    }

    function clickHome(){
        window.location.href = '/dashboard';
    }

    $(function() {

        $('input[name="datetimes"]').daterangepicker({
            maxDate: new Date(),
            startDate: from,
            endDate: to,
            locale: {
                format: 'DD/MM/YYYY'
            }
        });
    });

    $('input[name="datetimes"]').on('apply.daterangepicker', function(ev, picker) {
        window.location.href = '/transactions?from='+picker.startDate.format('YYYY/MM/DD')+'&to='+picker.endDate.format('YYYY/MM/DD');
    });
</script>
