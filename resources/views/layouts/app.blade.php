<head>
    <meta charset="UTF-8">
    <title>@yield('title')</title>
    <style>
        body {
            direction: ltr;
            margin: 30px;
            font-family: sans-serif;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
        }

        th, td {
            padding: 10px;
            border: 1px solid #ddd;
        }

        th {
            background-color: #f4f4f4;
        }

        .status-approved { color: green; }
        .status-rejected { color: red; }
        .status-pending  { color: orange; }

        /* 🎯 استایل‌دهی به فرم */
        #expenseForm {
            max-width: 600px;
            margin: 0 auto;
            background-color: #fefefe;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 6px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        }

        #expenseForm label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
            color: #333;
        }

        #expenseForm input[type="text"],
        #expenseForm input[type="number"],
        #expenseForm input[type="file"],
        #expenseForm textarea,
        #expenseForm select {
            width: 100%;
            padding: 8px 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
            box-sizing: border-box;
        }

        #expenseForm textarea {
            resize: vertical;
            min-height: 80px;
        }

        #expenseForm button[type="submit"] {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #28a745;
            border: none;
            color: white;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
        }

        #expenseForm button[type="submit"]:hover {
            background-color: #218838;
        }

        #validate, #result {
            max-width: 600px;
            margin: 10px auto;
            font-size: 14px;
        }
        h3{
            direction: rtl;
        }
        .index {
            max-width: 600px;
            margin: 40px auto;
            padding: 30px;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            background-color: #f9f9f9;
            border: 2px dashed #ccc;
            border-radius: 8px;
            color: #333;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
    </style>

</head>
<body>
    @yield('content')
</body>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

