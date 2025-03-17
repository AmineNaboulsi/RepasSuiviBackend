<!DOCTYPE html>
<html>
<head>
    <title>Email Verification</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gray-100 font-sans text-gray-800">
    <div class="max-w-md mx-auto my-8 bg-white rounded-lg shadow-md overflow-hidden">
        <div class="text-center p-6 bg-blue-50">
            <h2 class="text-2xl font-bold text-blue-800">Welcome to Our Service, {{ $name }}!</h2>
        </div>

        <div class="p-6">
            <p class="mb-6">Thank you for signing up. To get started, please verify your email address by clicking the button below:</p>

            <div class="text-center mb-6">
                <a href="{{ url($verification_link) }}" class="inline-block px-6 py-3 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 transition duration-150">Verify My Email</a>
            </div>

            <p class="mb-2 text-sm">If the button doesn't work, you can also copy and paste the following link into your browser:</p>
            <p class="mb-6 text-sm text-gray-600 break-all">{{ url($verification_link) }}</p>

            <p class="text-sm text-gray-700">This link will expire in 24 hours.</p>
        </div>

        <div class="mt-6 p-6 text-center text-xs text-gray-500 border-t border-gray-200">
            <p class="mb-2">If you didn't create an account, you can safely ignore this email.</p>
            <p>&copy; {{ date('Y') }} RepasSuivi. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
