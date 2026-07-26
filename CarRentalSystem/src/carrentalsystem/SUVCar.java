package carrentalsystem;

public class SUVCar extends Vehicle {
    private boolean isFourWheelDrive;

    public SUVCar() { super(); this.isFourWheelDrive = false; }

    public SUVCar(String id, String brand, String model, int year, String plate, String trans,
                  int seats, double mileage, String status, double rate, boolean is4wd) {
        super(id, brand, model, year, plate, trans, seats, mileage, status, rate);
        this.isFourWheelDrive = is4wd;
    }

    public boolean isFourWheelDrive() { return isFourWheelDrive; }
    public void setFourWheelDrive(boolean fourWheelDrive) { isFourWheelDrive = fourWheelDrive; }

    @Override public String getCategory() { return "SUV"; }

    @Override
    public double calculateRentalCost(int days) { return getDailyRate() * days * 1.10; }

    @Override public String toString() { return super.toString() + " | 4WD:" + (isFourWheelDrive ? "Yes" : "No"); }

    @Override public boolean equals(Object obj) {
        if (this == obj) return true;
        if (obj == null || !(obj instanceof SUVCar)) return false;
        return super.equals(obj);
    }
}
