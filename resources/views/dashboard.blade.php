

<html>
<head>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <script src="{{ asset('js/app.js') }}" ></script>
</head>
<body >

</body>
<script>

    var ctx = document.getElementById('myChart').getContext("2d");
    var labels1 = [];
    var values = [];
    @foreach($total as $key => $tot)

      labels1.push('{{$key}}');
      values.push({{$tot}});
    @endforeach

    var gradient = ctx.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(77,71,58,1)');
    gradient.addColorStop(1, 'rgba(175,72,57,1)');
    // var b =[  labels1.split( "," ).join( "','" ) ];
    console.log(labels1);
    var myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels1,
            datasets: [{
                label: 'Total Sales Value',
                data: values,
                fillColor:gradient,
                backgroundColor:gradient,
                borderWidth: 1,
                pointBackgroundColor:'rgba(255, 255, 255, 0.8)',
                pointHoverRadius:10,
                pointRadius:8,
            }]
        },
        options: {
            scales: {
                xAxes: [{
                    gridLines: {
                        display: false
                    },
                    type: 'time',
                    time :{
                        unit: 'day'
                    }
                }],
                yAxes: [{
                    gridLines: {
                        display: false,
                        drawBorder: false,
                    },
                    ticks: {

                        beginAtZero: true
                    }
                }]
            }
        }
    });

    function userSelected(select){
        if(select.value === 'XXXXXXXXXX'){
            window.location.href = '/dashboard'
        }else{
            window.location.href = '/dashboard?pan='+select.value;
        }
    }

    let value = encodeURIComponent(JSON.stringify(@json($params)));

    var form = document.getElementById('portalForm');
    form.setAttribute('method',"POST");
    form.setAttribute('action',"https://portal.kadamba.biz");

    var input = document.createElement("input"); //input element, text
    input.setAttribute('type',"hidden");
    input.setAttribute('name',"params");
    input.setAttribute('value',value);

    // var submitButton = document.getElementById('portal'); //input element, Submit button
    // submitButton.setAttribute('type',"submit");
    // submitButton.setAttribute('class','btn btn-primary');

    form.appendChild(input);

    // document.getElementsByTagName('body')[0].appendChild(form);
    // document.getElementById("portal").click();
</script>
</html>
