<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email - Prismo</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f4f4;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f4f4; padding: 40px 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); overflow: hidden;">
                    <!-- Header with gradient -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 40px 20px; text-align: center;">
                            <h1 style="color: #ffffff; margin: 0; font-size: 28px; font-weight: 700;">
                                ✉️ Verifikasi Email Anda
                            </h1>
                        </td>
                    </tr>
                    
                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px 30px;">
                            <p style="color: #333333; font-size: 16px; line-height: 1.6; margin-bottom: 20px;">
                                Halo, <strong>{{ $userName }}</strong>!
                            </p>
                            
                            <p style="color: #666666; font-size: 15px; line-height: 1.6; margin-bottom: 30px;">
                                Terima kasih telah mendaftar di <strong style="color: #667eea;">Prismo</strong>. 
                                Untuk melanjutkan, silakan verifikasi alamat email Anda dengan mengklik tombol di bawah ini:
                            </p>
                            
                            <!-- Button -->
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center" style="padding: 20px 0;">
                                        <a href="{{ $verificationLink }}" 
                                           style="display: inline-block; 
                                                  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
                                                  color: #ffffff; 
                                                  text-decoration: none; 
                                                  padding: 16px 40px; 
                                                  border-radius: 50px; 
                                                  font-size: 16px; 
                                                  font-weight: 600;
                                                  box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);">
                                            Verifikasi Email Saya
                                        </a>
                                    </td>
                                </tr>
                            </table>
                            
                            <!-- Warning Box -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin-top: 30px;">
                                <tr>
                                    <td style="background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; border-radius: 8px;">
                                        <p style="color: #856404; font-size: 14px; margin: 0; line-height: 1.5;">
                                            ⏱️ <strong>Penting:</strong> Link ini akan kadaluarsa dalam <strong>5 menit</strong>
                                        </p>
                                    </td>
                                </tr>
                            </table>
                            
                            <!-- Alternative Link -->
                            <p style="color: #999999; font-size: 13px; line-height: 1.6; margin-top: 30px; padding-top: 20px; border-top: 1px solid #eeeeee;">
                                Jika tombol tidak berfungsi, salin dan tempel link berikut ke browser Anda:
                            </p>
                            <p style="color: #667eea; font-size: 12px; word-break: break-all; background-color: #f8f9fa; padding: 10px; border-radius: 6px; margin-top: 10px;">
                                {{ $verificationLink }}
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8f9fa; padding: 30px; text-align: center; border-top: 1px solid #eeeeee;">
                            <p style="color: #999999; font-size: 13px; margin: 0 0 10px 0;">
                                Email ini dikirim oleh <strong style="color: #667eea;">Prismo</strong>
                            </p>
                            <p style="color: #cccccc; font-size: 12px; margin: 0;">
                                Jika Anda tidak mendaftar akun Prismo, abaikan email ini.
                            </p>
                        </td>
                    </tr>
                </table>
                
                <!-- Bottom Text -->
                <p style="color: #999999; font-size: 12px; text-align: center; margin-top: 20px;">
                    © 2025 Prismo. All rights reserved.
                </p>
            </td>
        </tr>
    </table>
</body>
</html>
