@extends('layouts.app')
@section('title' , 'create expense')
@section('content')
<form id="expenseForm" enctype="multipart/form-data" >
    <label>expense category:</label>
    <select name="category_id" id="categorySelect" required>
        <option value="">در حال بارگذاری...</option>
    </select>

    <label>description
    <textarea name="description" required >
    </textarea></label>

    <label>ammount</label>
    <input type="number" name="amount" value="" required>

    <label>iban</label>
    <input type="text" name="iban" value="" required>

    <label>national code</label>
    <input type="text" name="national_code" value="" required>

    <label>attachment file</label>
    <input type="file" name="attachment" accept=".pdf,.jpg,.png">

    <button type="submit">submit</button>
</form>

<div id="validate" style="color: red"></div>
<div id="result" style="color: green"></div>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('expenseForm');
        const categorySelect = document.getElementById('categorySelect');
        const validateDiv = document.getElementById('validate');
        const resultDiv = document.getElementById('result');

        axios.get('{{route('expenses.categories')}}')
            .then(res => {
                categorySelect.innerHTML = '<option value="">انتخاب کنید</option>';
                res.data.forEach(cat => {
                    const opt = document.createElement('option');
                    opt.value = cat.id;
                    opt.textContent = cat.name;
                    categorySelect.appendChild(opt);
                });
            })
            .catch(err => {
                categorySelect.innerHTML = '<option>خطا در دریافت دسته‌بندی</option>';
            });

        // ارسال فرم
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(form);

            axios.post('{{route("expenses.store")}}', formData, {
                headers: {
                    // 'Content-Type': 'multipart/form-data'
                }
            })
                .then(res => {
                    resultDiv.innerText = res.data.message;
                    form.reset();
                })
                .catch(err => {
                    console.log(err)
                    const errors = err.response.data.errors;
                    for (let field in errors) {
                        errors[field].forEach(msg => {
                            validateDiv.innerHTML += `<p>${msg}</p>`;}
                        )}
                });
        });
    });
</script>
@endsection
