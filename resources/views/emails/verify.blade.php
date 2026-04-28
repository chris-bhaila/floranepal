<x-mail::message>

# Hello, {{ $user->name }}! 👋

Thanks for joining **FloraNepal**. Please verify your email address to activate your account.

<x-mail::button :url="$url" color="success">
Verify Email Address
</x-mail::button>

This link will expire in **60 minutes**.

If you didn't create an account with FloraNepal, you can safely ignore this email.

Thanks,
**The FloraNepal Team**

<small style="color: #999;">
If the button doesn't work, copy and paste this link into your browser:<br>
{{ $url }}
</small>

</x-mail::message>