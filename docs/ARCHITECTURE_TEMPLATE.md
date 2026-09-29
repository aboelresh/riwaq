\# Architecture Template — Riwaq Backend



\## القاعدة الأساسية

كل Controller في المشروع ده لازم يتبع النمط ده بدون استثناء:

HTTP Request

↓

Controller (5-15 سطر بس)

↓

FormRequest (validation)

↓

Service (business logic)

↓

Resource (response shape)

↓

JSON Response

\---



\## 1. FormRequest — القواعد



\*\*مكان الملف:\*\* `app/Http/Requests/{Module}/{ActionName}Request.php`



\*\*مثال:\*\* `app/Http/Requests/Team/CreateTeamRequest.php`



\*\*Template:\*\*

```php

<?php



namespace App\\Http\\Requests\\Team;



use App\\Enums\\TeamType;

use Illuminate\\Foundation\\Http\\FormRequest;



class CreateTeamRequest extends FormRequest

{

&#x20;   public function authorize(): bool

&#x20;   {

&#x20;       return true; // Authorization في الـ Controller أو Policy

&#x20;   }



&#x20;   public function rules(): array

&#x20;   {

&#x20;       return \[

&#x20;           'name' => 'required|string|max:100',

&#x20;           'type' => 'required|in:' . TeamType::validationValues(),

&#x20;           // ...

&#x20;       ];

&#x20;   }



&#x20;   public function messages(): array

&#x20;   {

&#x20;       return \[

&#x20;           'type.in' => 'Team type must be: ' . TeamType::validationValues(),

&#x20;       ];

&#x20;   }

}

```



\*\*القواعد:\*\*

\- لا `Validator::make()` في الـ Controller أبداً

\- لا inline validation من أي نوع

\- كل custom messages في `messages()` method

\- الـ `authorize()` دايماً `return true` — الـ authorization في Controller/Policy



\---



\## 2. API Resource — القواعد



\*\*مكان الملف:\*\* `app/Http/Resources/{Model}Resource.php`



\*\*مثال:\*\* `app/Http/Resources/UserResource.php`



\*\*Template:\*\*

```php

<?php



namespace App\\Http\\Resources;



use Illuminate\\Http\\Resources\\Json\\JsonResource;



class UserResource extends JsonResource

{

&#x20;   public function toArray($request): array

&#x20;   {

&#x20;       return \[

&#x20;           'id'                => $this->id,

&#x20;           'name'              => $this->name,

&#x20;           'email'             => $this->email,

&#x20;           // لا password, لا verification\_code, لا remember\_token

&#x20;       ];

&#x20;   }

}

```



\*\*القواعد:\*\*

\- لا `$user` يرجع raw من Controller أبداً

\- كل field بيظهر في الـ response لازم يكون مكتوب صريح هنا

\- السلبي أهم من الإيجابي — اكتب اللي هيظهر، مش اللي هيتخبى



\---



\## 3. Controller — القواعد



\*\*Template:\*\*

```php

public function store(CreateTeamRequest $request): JsonResponse

{

&#x20;   $team = $this->teamService->createTeam(

&#x20;       $request->validated(),

&#x20;       auth()->user()

&#x20;   );



&#x20;   return response()->json(\[

&#x20;       'success' => true,

&#x20;       'message' => 'Team created successfully',

&#x20;       'data'    => new TeamResource($team),

&#x20;   ], 201);

}

```



\*\*القواعد:\*\*

\- لا `try/catch` في الـ Controller — الـ Exception Handler بيمسكها

\- لا `Validator::make()` — في الـ FormRequest

\- لا `$e->getMessage()` في أي مكان

\- الـ Controller بيعمل 3 حاجات بس: يستقبل، يفوّض للـ Service، يرجع Resource

\- Max 15 سطر لكل method



\---



\## 4. Service — القواعد



\*\*مكان الملف:\*\* `app/Services/{Module}Service.php`



\*\*القواعد:\*\*

\- كل business logic هنا

\- يستقبل `array $data` مش `Request $request`

\- يرجع Model أو Collection — مش Response

\- Multi-step DB operations: دايماً جوه `DB::transaction()`



\---



\## ترتيب التطبيق



لكل Controller method:

1\. أنشئ الـ FormRequest أولاً

2\. أنشئ الـ Resource لو مفيش

3\. انقل الـ logic للـ Service

4\. نضّف الـ Controller

5\. اختبر Postman — نفس الـ response shape

6\. Git commit

