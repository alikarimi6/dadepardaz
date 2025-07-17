@extends('layouts.app')
@section('title', 'admin panel')
@section('content')
    <button class="bulk-action-btn" data-route="{{ route('expenses.bulk.approve') }}">✔️ تایید گروهی</button>
    <button class="bulk-action-btn" data-route="{{ route('expenses.bulk.reject') }}">❌ رد گروهی</button>

    <table id="expenses-table">
    <thead>
    <tr>
        <th></th>
        <th>id</th>
        <th>amount</th>
        <th>status</th>
        <th>rejection comment</th>
        <th>user</th>
        <th>category</th>
        <th>attachment</th>
        <th>action</th>
    </tr>
    </thead>
    <tbody>
    </tbody>
</table>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        axios.get('/api/v1/expenses')
            .then(function (response) {
                const downloadBaseUrl = "{{ route('expense.attachment.download', ['filepath' => '__FILENAME__']) }}";
                const data = response.data;
                const tbody = document.querySelector("#expenses-table tbody");
                data.forEach(expense => {
                    const tr = document.createElement("tr");
                    const attachment = downloadBaseUrl.replace('__FILENAME__', encodeURIComponent(expense.attachment));
                    tr.innerHTML = `
                            <td><input type="checkbox" class="select-row" value="${expense.id}"></td>
                            <td>${expense.id}</td>
                            <td>${Number(expense.amount).toLocaleString()}</td>
                            <td class="status-${expense.status}">${translateStatus(expense.status)}</td>
                            <td>${expense.rejection_comment}</td>
                            <td>${expense.user}</td>
                            <td>${expense.category}</td>
                            <td>
                                ${expense.attachment
                        ? `<a href="${attachment}" target="_blank">download</a>`
                        : '<em>none</em>'
                    }
                            </td>
                            <td>
                                <button onclick="approveExpense(${expense.id})">approve ✅</button>
                                <button onclick="rejectExpense(${expense.id})">reject ❌</button>
                            </td>
                        `;

                    tbody.appendChild(tr);
                });
            })
            .catch(function (error) {
                console.error(error);
            });

        function translateStatus(status) {
            switch (status) {
                case 'approved': return 'تایید شده';
                case 'rejected': return 'رد شده';
                case 'pending': return 'در انتظار تایید';
                default: return status;
            }
        }


        window.approveExpense = function(id) {
            const approveUri = `/api/v1/expenses/${id}/approve`;
            const userInput = prompt('Please choose:\n1 - schedule\n2 - manual');
            let paymentMethod;

            if (userInput === '1') {
                paymentMethod = 'scheduled';
            } else if (userInput === '2') {
                paymentMethod = 'manual';
            } else {
                alert('ورودی نامعتبر است. فقط عدد 1 یا 2 را وارد کنید.');
                return;
            }

            console.log('User selected:', paymentMethod);
            axios.post(approveUri , {payment_method: paymentMethod})
                .then(res => {
                    location.reload();
                })
                .catch(err => {
                    console.error(err);
                    alert('خطا در تایید');
                });
        };

        window.rejectExpense = function(id) {
            const rejectUri = `/api/v1/expenses/${id}/reject`;
            const reason = prompt("لطفاً دلیل رد را وارد کنید:");
            if (!reason) return;

            axios.post(rejectUri, { rejection_comment: reason })
                .then(res => {
                    location.reload();
                })
                .catch(err => {
                    console.error(err);
                    alert('error');
                });
        }

    });

//bulk

    document.querySelectorAll('.bulk-action-btn').forEach(button => {
        button.addEventListener('click', function () {
            const url = this.dataset.route;
            sendBulkActionTo(url);
        });
    });
    function getSelectedIds() {
        return Array.from(document.querySelectorAll('.select-row:checked')).map(cb => cb.value);
    }

    function sendBulkActionTo(url) {
        const ids = getSelectedIds();
        if (ids.length === 0) {
            alert("هیچ آیتمی انتخاب نشده!");
            return;
        }
        let reason = null;
        let paymentMethod = null;
        if(url === "{{ route('expenses.bulk.reject') }}") {
            reason = prompt("لطفاً دلیل رد را وارد کنید:");
        }else{
            const userInput = prompt('Please choose:\n1 - schedule\n2 - manual');
            if (userInput === '1') {
                paymentMethod = 'scheduled';
            } else if (userInput === '2') {
                paymentMethod = 'manual';
            } else {
                alert('ورودی نامعتبر است. فقط عدد 1 یا 2 را وارد کنید.');
                return;
            }
        }

        fetch(url, {
            method: "POST",
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                ids,
                ...(reason && reason.trim() !== '' && { rejection_comment: reason }) ,
                ...(paymentMethod && paymentMethod.trim() !== '' && { payment_method: paymentMethod })
            })
        })
            .then(res => res.json())
            .then(data => {
                location.reload();
            });
    }

// approve bank

</script>
@endsection
