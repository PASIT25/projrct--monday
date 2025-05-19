#include <iostream>
using namespace std;

int main() {
    int n;
    char poin;
    float total = 0;

    cout << "กรอกจำนวนนักเรียน: ";
    cin >> n;

    for (int i = 1; i <= n; i++) {
        cout << "กรอกเกรดของนักเรียนคนที่ " << i << " (A, B, C, D, F): ";
        cin >> poin;

        switch (toupper(poin)) {
            case 'A': total += 4.0; break;
            case 'B': total += 3.0; break;
            case 'C': total += 2.0; break;
            case 'D': total += 1.0; break;
            case 'F': total += 0.0; break;
            default:
                cout << "เกรดไม่ถูกต้อง! นับเป็น 0 คะแนน" << endl;
                total += 0.0;
        }
    }

    float average = total / n;
    cout << "ค่าเฉลี่ยเกรดของนักเรียนคือ: " << average << endl;

    return 0;
}