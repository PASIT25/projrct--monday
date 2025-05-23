#include <iostream>
#include <iomanip>
#include <sstream>
using namespace std;

int main() {
    const int MAX = 100;

    string code[MAX];       
    string name[MAX];       
    float price[MAX];       
    int quantity[MAX];      

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

        cout << "ราคา: ";
        cin >> price[i];

        cout << "จำนวน: ";
        cin >> quantity[i];
    }

    cout << "\n"
         << left
         << setw(12) << "รหัสสินค้า"
         << setw(20) << "ชื่อสินค้า"
         << setw(16) << "ราคา"
         << setw(10) << "จำนวน"
         << setw(14) << "มูลค่ารวม"
         << endl;

    cout << string(72, '-') << endl;

    cout << fixed << setprecision(2);
    float grandTotal = 0;

    for (int i = 0; i < n; i++) {
        float total = price[i] * quantity[i];
        grandTotal += total;

        cout << left
             << setw(12) << code[i]
             << setw(20) << name[i]
             << setw(16) << price[i]
             << setw(10) << quantity[i]
             << setw(14) << total
             << endl;
    }

    cout << string(72, '-') << endl;
    cout << right << setw(58) << "รวมทั้งหมด: " << setw(14) << grandTotal << endl;

    return 0;
}
