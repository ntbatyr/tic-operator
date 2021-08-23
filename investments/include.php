<script>
    $(() => {
        $('form input[type="checkbox"]').on('change', (e) => {
            let deposit = parseFloat($(e.target).closest('div.row').find('input[type="numeric"]').val());
            let total = $('#total-amount').text();

            total = parseFloat(total.replace(/[ ]/g, ''));

            console.log(deposit, total);

            if ($(e.target).is(':checked'))
                total += deposit;
            else
                total -= deposit;

            total = new Intl.NumberFormat('ru-RU', {style: 'currency', currency: 'RUB'})
                .format(total)
                .toString().replace(',', '.');

            $('#total-amount').text(total);
        });
    });
</script>