#!/bin/bash

# مسار مشروع Laravel - عدّله حسب مشروعك
PROJECT_PATH="/c/xampp/htdocs/clinic_test"

# تأكد أن المسار موجود
if [ ! -d "$PROJECT_PATH" ]; then
    echo "❌ المسار غير موجود: $PROJECT_PATH"
    exit 1
fi

# أمر الكرون الذي سيُضاف
CRON_JOB="* * * * * cd $PROJECT_PATH && php artisan schedule:run >> /dev/null 2>&1"

# تحقق إن كان موجود مسبقًا لتجنّب التكرار
(crontab -l | grep -F "$CRON_JOB") && EXISTS=true || EXISTS=false

if [ "$EXISTS" = true ]; then
    echo "✅ الكرون موجود بالفعل!"
else
    # أضف الكرون
    (crontab -l; echo "$CRON_JOB") | crontab -
    echo "🚀 تم إضافة الكرون بنجاح!"
fi

# عرض قائمة الكرون الحالية للتأكيد
echo "📋 الكرون الحالية:"
crontab -l
