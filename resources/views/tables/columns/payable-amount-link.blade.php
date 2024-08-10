@php
    $record = $getRecord();
    $amount = number_format($record->payable_amount, 2, '.', '');
    $upiId = $record->upi_id;
    $phonePeUrl = "phonepe://pay?pa={$upiId}&pn=Salary&am={$amount}&cu=INR";
    $alternate = "https://www.wikipedia.org";
@endphp

<a 
    href="#"
    onclick="handlePaymentClick(event, '{{ $phonePeUrl }}', '{{ $alternate }}')"
    class="text-primary-600 hover:text-primary-500"
>
    ₹ {{ $amount }}
</a>