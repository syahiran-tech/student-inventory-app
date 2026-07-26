package carrentalsystem;

public class Staff extends Person {
    private String position;
    private double salary;
    private static int staffCount = 0;

    public Staff() { super(); this.position = ""; this.salary = 0.0; staffCount++; }

    public Staff(String id, String username, String password, String name, String phone, String email, String position, double salary) {
        super(id, username, password, name, phone, email);
        this.position = position; this.salary = salary; staffCount++;
    }

    public String getPosition() { return position; }
    public void setPosition(String position) { this.position = position; }
    public double getSalary() { return salary; }
    public void setSalary(double salary) { this.salary = salary; }
    public static int getStaffCount() { return staffCount; }

    @Override
    public String getRole() { return "Staff"; }

    @Override
    public String toString() {
        return super.toString() + String.format(" | Position: %s | Salary: RM%.2f", position, salary);
    }

    @Override
    public boolean equals(Object obj) {
        if (this == obj) return true;
        if (obj == null || !(obj instanceof Staff)) return false;
        return super.equals(obj);
    }
}
