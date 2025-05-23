#include <iostream>
#include <iomanip>
#include <sstream> 
using namespace std;

int main() {
    const int MAX_ITEMS = 100;

    string code[MAX_ITEMS];       
    string name[MAX_ITEMS];       
    float price[MAX_ITEMS];       
    int quantity[MAX_ITEMS];      

    int n; 
    cout << "ต้องการกรอกกี่รายการ? ";
    cin >> n;

    for (int i = 0; i < n; i++) {
        stringstream ss;
        ss << "A" << setw(3) << setfill('0') << (i + 1);
        code[i] = ss.str();

        cout << "\nรายการที่ " << i + 1 << ":\n";
        cout << "ชื่อสินค้า: ";
        cin >> ws; 
        getline(cin, name[i]);

        cout << "ราคาต่อหน่วย: ";
        cin >> price[i];

        cout << "จำนวน: ";
        cin >> quantity[i];
    }

    cout << "\nรหัสสินค้า\tชื่อสินค้า\tราคาต่อหน่วย\tจำนวน\tมูลค่ารวม\n";
    cout << fixed << setprecision(2);

    for (int i = 0; i < n; i++) {
        float total = price[i] * quantity[i];
        cout << code[i] << "\t\t"
             << name[i] << "\t"
             << price[i] << "\t\t"
             << quantity[i] << "\t"
             << total << endl;
    }

    return 0;
}
