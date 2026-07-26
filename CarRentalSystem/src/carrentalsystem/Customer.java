package carrentalsystem;

public class Customer extends Person {
    private License license;
    private static int customerCount = 0;

    public Customer() { super(); this.license = null; customerCount++; }

    public Customer(String id, String username, String password, String name, String phone, String email, License license) {
        super(id, username, password, name, phone, email);
        this.license = license; customerCount++;
    }

    public License getLicense() { return license; }
    public void setLicense(License license) { this.license = license; }
    public static int getCustomerCount() { return customerCount; }

    @Override
    public String getRole() { return "Customer"; }

    @Override
    public String toString() {
        String licInfo = (license != null) ? license.toString() : "No License";
        return super.toString() + " | License: [" + licInfo + "]";
    }

    @Override
    public boolean equals(Object obj) {
        if (this == obj) return true;
        if (obj == null || !(obj instanceof Customer)) return false;
        return super.equals(obj);
    }
}
