package carrentalsystem;

public class Manager extends Person {
    private static int managerCount = 0;

    public Manager() { super(); managerCount++; }

    public Manager(String id, String username, String password, String name, String phone, String email) {
        super(id, username, password, name, phone, email);
        managerCount++;
    }

    public static int getManagerCount() { return managerCount; }

    @Override
    public String getRole() { return "Manager"; }

    @Override
    public String toString() { return super.toString(); }

    @Override
    public boolean equals(Object obj) {
        if (this == obj) return true;
        if (obj == null || !(obj instanceof Manager)) return false;
        return super.equals(obj);
    }
}
