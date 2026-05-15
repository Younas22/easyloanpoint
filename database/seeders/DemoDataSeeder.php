<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Loan;
use App\Models\LoanStatusHistory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // HR users
        $hrUsers = [];
        $hrData  = [
            ['name' => 'Rajesh Kumar',  'email' => 'rajesh.hr@easyloanpoint.com', 'phone' => '9811111111'],
            ['name' => 'Priya Sharma',  'email' => 'priya.hr@easyloanpoint.com',  'phone' => '9822222222'],
            ['name' => 'Amit Verma',    'email' => 'amit.hr@easyloanpoint.com',   'phone' => '9833333333'],
        ];

        foreach ($hrData as $data) {
            $hrUsers[] = User::updateOrCreate(
                ['email' => $data['email']],
                array_merge($data, ['password' => Hash::make('12345678'), 'role' => 'hr', 'status' => true])
            );
        }

        // Customers
        $customerData = [
            ['name' => 'Suresh Patel',      'phone' => '9001000001', 'email' => 'suresh@gmail.com',    'city' => 'Mumbai',    'state' => 'Maharashtra'],
            ['name' => 'Meena Devi',         'phone' => '9001000002', 'email' => 'meena@gmail.com',     'city' => 'Delhi',     'state' => 'Delhi'],
            ['name' => 'Ravi Tiwari',        'phone' => '9001000003', 'email' => 'ravi@gmail.com',      'city' => 'Lucknow',   'state' => 'UP'],
            ['name' => 'Anita Singh',        'phone' => '9001000004', 'email' => 'anita@gmail.com',     'city' => 'Jaipur',    'state' => 'Rajasthan'],
            ['name' => 'Manoj Yadav',        'phone' => '9001000005', 'email' => 'manoj@gmail.com',     'city' => 'Patna',     'state' => 'Bihar'],
            ['name' => 'Kavita Nair',        'phone' => '9001000006', 'email' => 'kavita@gmail.com',    'city' => 'Kochi',     'state' => 'Kerala'],
            ['name' => 'Deepak Mehta',       'phone' => '9001000007', 'email' => 'deepak@gmail.com',    'city' => 'Ahmedabad', 'state' => 'Gujarat'],
            ['name' => 'Sanjay Gupta',       'phone' => '9001000008', 'email' => 'sanjay@gmail.com',    'city' => 'Bhopal',    'state' => 'MP'],
            ['name' => 'Rekha Joshi',        'phone' => '9001000009', 'email' => 'rekha@gmail.com',     'city' => 'Pune',      'state' => 'Maharashtra'],
            ['name' => 'Vikram Chauhan',     'phone' => '9001000010', 'email' => 'vikram@gmail.com',    'city' => 'Chandigarh','state' => 'Punjab'],
            ['name' => 'Pooja Mishra',       'phone' => '9001000011', 'email' => 'pooja@gmail.com',     'city' => 'Varanasi',  'state' => 'UP'],
            ['name' => 'Arjun Reddy',        'phone' => '9001000012', 'email' => 'arjun@gmail.com',     'city' => 'Hyderabad', 'state' => 'Telangana'],
            ['name' => 'Nisha Kapoor',       'phone' => '9001000013', 'email' => 'nisha@gmail.com',     'city' => 'Chennai',   'state' => 'Tamil Nadu'],
            ['name' => 'Rahul Srivastava',   'phone' => '9001000014', 'email' => 'rahul@gmail.com',     'city' => 'Kanpur',    'state' => 'UP'],
            ['name' => 'Sunita Agarwal',     'phone' => '9001000015', 'email' => 'sunita@gmail.com',    'city' => 'Agra',      'state' => 'UP'],
            ['name' => 'Kiran Bose',         'phone' => '9001000016', 'email' => 'kiran@gmail.com',     'city' => 'Kolkata',   'state' => 'West Bengal'],
            ['name' => 'Naveen Sharma',      'phone' => '9001000017', 'email' => 'naveen@gmail.com',    'city' => 'Indore',    'state' => 'MP'],
            ['name' => 'Geeta Pandey',       'phone' => '9001000018', 'email' => 'geeta@gmail.com',     'city' => 'Nagpur',    'state' => 'Maharashtra'],
            ['name' => 'Ashok Rao',          'phone' => '9001000019', 'email' => 'ashok@gmail.com',     'city' => 'Bangalore', 'state' => 'Karnataka'],
            ['name' => 'Divya Thakur',       'phone' => '9001000020', 'email' => 'divya@gmail.com',     'city' => 'Surat',     'state' => 'Gujarat'],
        ];

        $customers = [];
        foreach ($customerData as $i => $data) {
            $customers[] = Customer::updateOrCreate(
                ['phone' => $data['phone']],
                array_merge($data, [
                    'status'     => 'active',
                    'created_by' => $hrUsers[$i % count($hrUsers)]->id,
                ])
            );
        }

        // Loan statuses to distribute realistically
        $statuses = [
            'pending', 'pending', 'pending',
            'under_review', 'under_review',
            'approved', 'approved', 'approved', 'approved',
            'rejected', 'rejected',
            'disbursed', 'disbursed', 'disbursed', 'disbursed',
            'pending', 'under_review', 'approved', 'rejected', 'disbursed',
        ];

        $loanTypes = ['personal', 'home', 'business', 'vehicle', 'education'];
        $purposes  = [
            'Home renovation', 'Medical expenses', 'Business expansion',
            'Vehicle purchase', 'Education fees', 'Debt consolidation',
            'Wedding expenses', 'Travel', 'Emergency fund',
        ];

        $admin = User::where('role', 'admin')->first();

        foreach ($customers as $i => $customer) {
            $status = $statuses[$i];
            $hrUser = $hrUsers[$i % count($hrUsers)];
            $createdAt = now()->subDays(rand(1, 180));

            $loan = new Loan([
                'customer_id'      => $customer->id,
                'assigned_hr_id'   => $hrUser->id,
                'loan_type'        => $loanTypes[$i % count($loanTypes)],
                'amount_requested' => rand(50, 2000) * 1000,
                'amount_approved'  => in_array($status, ['approved', 'disbursed']) ? rand(40, 1800) * 1000 : null,
                'interest_rate'    => in_array($status, ['approved', 'disbursed']) ? round(rand(8, 18) + rand(0, 9) / 10, 1) : null,
                'tenure_months'    => [12, 24, 36, 48, 60][$i % 5],
                'purpose'          => $purposes[$i % count($purposes)],
                'status'           => $status,
                'applied_at'       => $createdAt,
                'reviewed_at'      => in_array($status, ['approved', 'rejected', 'disbursed']) ? $createdAt->copy()->addDays(rand(2, 10)) : null,
                'disbursed_at'     => $status === 'disbursed' ? $createdAt->copy()->addDays(rand(12, 25)) : null,
            ]);

            $loan->loan_number = Loan::generateLoanNumber();
            $loan->save();
            $loan->created_at = $createdAt;
            $loan->save();

            // Status history
            LoanStatusHistory::create([
                'loan_id'    => $loan->id,
                'changed_by' => $admin->id,
                'from_status'=> null,
                'to_status'  => 'pending',
                'remarks'    => 'Loan application submitted.',
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            if (in_array($status, ['under_review', 'approved', 'rejected', 'disbursed'])) {
                LoanStatusHistory::create([
                    'loan_id'    => $loan->id,
                    'changed_by' => $hrUser->id,
                    'from_status'=> 'pending',
                    'to_status'  => 'under_review',
                    'remarks'    => 'Documents received, under review.',
                    'created_at' => $createdAt->copy()->addDays(1),
                    'updated_at' => $createdAt->copy()->addDays(1),
                ]);
            }

            if (in_array($status, ['approved', 'rejected', 'disbursed'])) {
                LoanStatusHistory::create([
                    'loan_id'    => $loan->id,
                    'changed_by' => $hrUser->id,
                    'from_status'=> 'under_review',
                    'to_status'  => in_array($status, ['approved', 'disbursed']) ? 'approved' : 'rejected',
                    'remarks'    => in_array($status, ['approved', 'disbursed']) ? 'Loan approved after verification.' : 'Rejected due to insufficient documents.',
                ]);
            }
        }

        $this->command->info('✓ Demo data seeded: 20 customers, 20 loans with histories');
    }
}
